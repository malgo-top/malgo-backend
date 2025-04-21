<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\PaymentHasBill;
use App\Models\Bill;
use App\Models\BillType;
use App\Models\Property;
use App\Models\RentContract;
use App\Models\WaterBill;
use App\Models\GasBill;
use App\Models\ElectricityBill;
use App\Mail\BillCreated;
use App\Models\User;

class BillController extends Controller
{

    public function payBills(Request $request)
    {
        $request->validate([
            'paymentData.amount' => 'required|numeric',
            'paymentData.payment_method' => 'required|string',
            'paymentData.payment_proof' => 'required|image|mimes:jpg,jpeg,png,pdf',
            'billsPaid' => 'required|array',
            'billsPaid.*' => 'exists:bills,id'
        ]);

        // Step 1: Upload proof to S3
        $proof = $request->file('paymentData.payment_proof');
        $proofUrl = $proof->store('payment_proofs', 's3');
        // $proofUrl = Storage::disk('s3')->url($path);

        // Step 2: Create the payment
        $payment = Payment::create([
            'amount' => $request->paymentData['amount'],
            'payment_method' => $request->paymentData['payment_method'],
            'status' => 'Pendiente',
            'paid_at' => now(),
            'payment_cert' => $proofUrl
        ]);

        // Step 3: Link each bill to the payment
        foreach ($request->billsPaid as $billId) {
            PaymentHasBill::create([
                'payment_id' => $payment->id,
                'bill_id' => $billId
            ]);

            // Step 4: Mark bill as paid
            $bill = Bill::find($billId);
            $bill->status = 'Pagado';
            $bill->save();
        }

        return response()->json([
            "success" => true,
        ]);
    }

    public function getBillsUnpaid()
    {
        $user = auth()->user();

        $query = Bill::whereIn('status', ['Pendiente', 'Atrasado'])
                    ->with([
                        'property:id,sku',
                        'billType:id,name'
                    ]);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id); // adjust this if the relation is indirect
        }

        $bills = $query->get();

        return response()->json(["success" => true, "data" => $bills]);
    }

    public function createWaterBill(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'due_date' => 'required|date',
            'metros_cubicos_201' => 'required|numeric',
            'metros_cubicos_301' => 'required|numeric',
            'acueducto_cargo_fijo' => 'required|numeric',
            'acueducto_consumo_residencial_basico_quantity' => 'required|numeric',
            'acueducto_consumo_residencial_basico_subtotal' => 'required|numeric',
            'acueducto_consumo_residencial_superior_quantity' => 'required|numeric',
            'acueducto_consumo_residencial_superior_subtotal' => 'required|numeric',
            'alcantarillado_cargo_fijo' => 'required|numeric',
            'alcantarillado_consumo_residencial_basico_quantity' => 'required|numeric',
            'alcantarillado_consumo_residencial_basico_subtotal' => 'required|numeric',
            'alcantarillado_consumo_residencial_superior_quantity' => 'required|numeric',
            'alcantarillado_consumo_residencial_superior_subtotal' => 'required|numeric',
            'bill_doc' => 'file'
        ]);

        // // Save uploaded file
        // $filePath = $request->file('bill_doc')->store('bills', 'public');

        if($request->file('bill_doc')){
            $filePath = $request->file('bill_doc')->store('bills', 's3');
        }
        // $filePath = Storage::disk('s3')->url($file);

        // Get latest water bill to calculate usage
        $latestWaterBill = WaterBill::latest()->first();

        $metrosUsados201 = $validated['metros_cubicos_201'] - ($latestWaterBill->metros_cubicos_201 ?? 0);
        $metrosUsados301 = $validated['metros_cubicos_301'] - ($latestWaterBill->metros_cubicos_301 ?? 0);

        $cargoFijo = ($validated['acueducto_cargo_fijo'] / 3) + ($validated['alcantarillado_cargo_fijo'] / 3);

        $acueductoPrecioMetro = (
            $validated['acueducto_consumo_residencial_superior_subtotal'] +
            $validated['acueducto_consumo_residencial_basico_subtotal']
        ) / (
            $validated['acueducto_consumo_residencial_superior_quantity'] +
            $validated['acueducto_consumo_residencial_basico_quantity']
        );

        $alcantarilladoPrecioMetro = (
            $validated['alcantarillado_consumo_residencial_superior_subtotal'] +
            $validated['alcantarillado_consumo_residencial_basico_subtotal']
        ) / (
            $validated['alcantarillado_consumo_residencial_superior_quantity'] +
            $validated['alcantarillado_consumo_residencial_basico_quantity']
        );

        // Get properties
        $property201 = Property::whereEncrypted('sku', 'FO-APTO-201')->firstOrFail();
        $property301 = Property::whereEncrypted('sku', 'FO-APTO-301')->firstOrFail();

        // Get user from rent contracts
        $user201 = RentContract::where('property_id', $property201->id)
            ->where('status', 'En curso')
            ->first()->user_id ?? 1;

        $user301 = RentContract::where('property_id', $property301->id)
            ->where('status', 'En curso')
            ->first()->user_id ?? 1;

        // Calculate total amount
        $amount201 = $cargoFijo + ($metrosUsados201 * $acueductoPrecioMetro) + ($metrosUsados201 * $alcantarilladoPrecioMetro);
        $amount301 = $cargoFijo + ($metrosUsados301 * $acueductoPrecioMetro) + ($metrosUsados301 * $alcantarilladoPrecioMetro);

        // Create the water bill
        $waterBill = WaterBill::create([
            'metros_cubicos_201' => $validated['metros_cubicos_201'],
            'metros_cubicos_301' => $validated['metros_cubicos_301'],
            'acueducto_cargo_fijo' => $validated['acueducto_cargo_fijo'],
            'acueducto_consumo_residencial_basico_quantity' => $validated['acueducto_consumo_residencial_basico_quantity'],
            'acueducto_consumo_residencial_basico_subtotal' => $validated['acueducto_consumo_residencial_basico_subtotal'],
            'acueducto_consumo_residencial_superior_quantity' => $validated['acueducto_consumo_residencial_superior_quantity'],
            'acueducto_consumo_residencial_superior_subtotal' => $validated['acueducto_consumo_residencial_superior_subtotal'],
            'alcantarillado_cargo_fijo' => $validated['alcantarillado_cargo_fijo'],
            'alcantarillado_consumo_residencial_basico_quantity' => $validated['alcantarillado_consumo_residencial_basico_quantity'],
            'alcantarillado_consumo_residencial_basico_subtotal' => $validated['alcantarillado_consumo_residencial_basico_subtotal'],
            'alcantarillado_consumo_residencial_superior_quantity' => $validated['alcantarillado_consumo_residencial_superior_quantity'],
            'alcantarillado_consumo_residencial_superior_subtotal' => $validated['alcantarillado_consumo_residencial_superior_subtotal'],
            'bill_doc' => $filePath ?? null,
        ]);

        // Create or get "Agua" BillType
        $billType = BillType::Creat([
            'name' => 'Agua',
            "water_bill_id" => $waterBill->id
        ]);

        // Create two bills
        $bill201 = Bill::create([
            'bill_type_id' => $billType->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'due_date' => $validated['due_date'],
            'amount' => $amount201,
            'status' => 'Pendiente',
            'property_id' => $property201->id,
            'user_id' => $user201 ?? 1
        ]);

        $bill301 = Bill::create([
            'bill_type_id' => $billType->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'due_date' => $validated['due_date'],
            'amount' => $amount301,
            'status' => 'Pendiente',
            'property_id' => $property301->id,
            'user_id' => $user301 ?? 1
        ]);

        $user201Model = User::find($user201) ?? User::find(1);
        $user301Model = User::find($user301) ?? User::find(1);
        
        Mail::to($user201Model->email)->send(new BillCreated($user201Model, $bill201, 'Agua'));
        Mail::to($user301Model->email)->send(new BillCreated($user301Model, $bill301, 'Agua'));


        return response()->json([
            'success' => true,
        ]);
    }


    public function createGasBill(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'due_date' => 'required|date',
            'total' => 'required|numeric',
            'heads_201' => 'required|integer|min:1',
            'heads_301' => 'required|integer|min:1',
            'bill_doc' => 'file',
        ]);

        // Upload file to AWS S3
        if($request->file('bill_doc')){
            $filePath = $request->file('bill_doc')->store('gas_bills', 's3');
        }
        // $filePath = Storage::disk('s3')->url($file);

        // Create GasBill
        $gasBill = GasBill::create([
            'total' => $validated['total'],
            'heads_201' => $validated['heads_201'],
            'heads_301' => $validated['heads_301'],
            'bill_doc' => $filePath ?? null,
        ]);

        // Create BillType with relation to GasBill
        $billType = BillType::create([
            'name' => 'Gas',
            'gas_bill_id' => $gasBill->id,
        ]);

        $totalHeads = $validated['heads_201'] + $validated['heads_301'];
        $perHeadCost = $validated['total'] / $totalHeads;

        // Get properties
        $property201 = Property::whereEncrypted('sku', 'FO-APTO-201')->firstOrFail();
        $property301 = Property::whereEncrypted('sku', 'FO-APTO-301')->firstOrFail();

        // Get users from rent contracts
        $user201 = RentContract::where('property_id', $property201->id)
            ->where('status', 'En curso')
            ->first()->user_id ?? 1;
        
        $user301 = RentContract::where('property_id', $property301->id)
            ->where('status', 'En curso')
            ->first()->user_id ?? 1;
        
        // Calculate amounts
        $amount201 = $perHeadCost * $validated['heads_201'];
        $amount301 = $perHeadCost * $validated['heads_301'];

        // Create bills
        $bill201 = Bill::create([
            'bill_type_id' => $billType->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'due_date' => $validated['due_date'],
            'amount' => $amount201,
            'status' => 'Pendiente',
            'property_id' => $property201->id,
            'user_id' => $user201 ?? 1
        ]);

        $bill301 = Bill::create([
            'bill_type_id' => $billType->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'due_date' => $validated['due_date'],
            'amount' => $amount301,
            'status' => 'Pendiente',
            'property_id' => $property301->id,
            'user_id' => $user301 ?? 1
        ]);

        $user201Model = User::find($user201) ?? User::find(1);
        $user301Model = User::find($user301) ?? User::find(1);

        Mail::to($user201Model->email)->send(new BillCreated($user201Model, $bill201, 'Gas'));
        Mail::to($user301Model->email)->send(new BillCreated($user301Model, $bill301, 'Gas'));

        return response()->json([
            'success' => true
        ]);
    }

    public function createElectricityBills(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'due_date' => 'required|date',
            'total' => 'required|numeric',
            'aseo' => 'required|numeric',
            'heads_201' => 'required|integer|min:1',
            'heads_301' => 'required|integer|min:1',
            'heads_401' => 'required|integer|min:1',
            'bill_doc' => 'file',
        ]);

        // Upload bill_doc to AWS S3
        if($request->file('bill_doc')){
            $filePath = $request->file('bill_doc')->store('electricity_bills', 's3');
        }            
        // $filePath = Storage::disk('s3')->url($file);

        // Create ElectricityBill
        $electricityBill = ElectricityBill::create([
            'total' => $validated['total'],
            'aseo' => $validated['aseo'],
            'heads_201' => $validated['heads_201'],
            'heads_301' => $validated['heads_301'],
            'heads_401' => $validated['heads_401'],
            'bill_doc' => $filePath ?? null,
        ]);

        // Create BillType
        $billType = BillType::create([
            'name' => 'Luz',
            'electricity_bill_id' => $electricityBill->id,
        ]);

        $aseoShare = $validated['aseo'] / 3;
        $totalWithoutAseo = $validated['total'] - $validated['aseo'];
        $totalHeads = $validated['heads_201'] + $validated['heads_301'] + $validated['heads_401'];
        $perHeadCost = $totalWithoutAseo / $totalHeads;

        // Define apartments and heads
        $apts = [
            'FO-APTO-201' => $validated['heads_201'],
            'FO-APTO-301' => $validated['heads_301'],
        ];

        $createdBills = [];

        foreach ($apts as $sku => $heads) {
            $property = Property::whereEncrypted('sku', $sku)->firstOrFail();
            $userId = RentContract::where('property_id', $property->id)
                ->where('status', 'En curso')
                ->first()->user_id ?? 1;

            $amount = $aseoShare + ($heads * $perHeadCost);

            $bill = Bill::create([
                'bill_type_id' => $billType->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'due_date' => $validated['due_date'],
                'amount' => $amount,
                'status' => 'Pendiente',
                'property_id' => $property->id,
                'user_id' => $userId ?? 1,
            ]);

            $userModel = User::findOrFail($userId) ?? User::find(1);    

            Mail::to($userModel->email)->send(new BillCreated($userModel, $bill, 'Luz'));

            $createdBills[] = $bill;
        }


        return response()->json([
            'success' => true,
        ]);
    }

}

