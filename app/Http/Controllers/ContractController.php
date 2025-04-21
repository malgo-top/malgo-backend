<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\RentContract;
use App\Models\TenantApplication;
use App\Models\FinancialResponsible;
use App\Models\User;
use App\Mail\ContractSignedMail;
use App\Mail\TenantApplicationDenied;
use App\Models\Bill;
use App\Models\BillType;
use Carbon\Carbon;

class ContractController extends Controller
{

    public function createContract(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'monthly_amount' => 'required|numeric',
            'deposit_amount' => 'required|numeric',
            'tenant_application_id' => 'required|exists:tenant_applications,id',
            'property_id' => 'required|exists:properties,id',
            'contract_doc' => 'required|file|mimes:pdf,doc,docx'
        ]);

         // 🚫 Check if property already has an active contract
        $activeContract = RentContract::where('property_id', $request->property_id)
            ->where('status', 'En curso')
            ->first();

        if ($activeContract) {
            return response()->json([
                'error' => 'Ya existe un contrato activo para esta propiedad.',
                'success' => true
            ], 409); // 409 Conflict
        }

        // Get the tenant application and principal responsible
        $tenantApplication = TenantApplication::with('financialResponsibles')->findOrFail($request->tenant_application_id);
        $principal = $tenantApplication->financialResponsibles->where('principal', 1)->first();

        if (!$principal) {
            return response()->json(['error' => 'Principal financial responsible not found.'], 404);
        }

        $tenantApplication->status = "En Curso";
        $tenantApplication->save();

        // Upload the contract file to S3
        $file = $request->file('contract_doc');
        $contractUrl = $file->store('contracts', 's3');
        // $contractUrl = Storage::disk('s3')->url($path);

        // Create a new user for the principal financial responsible
        $password = Str::random(8);
        $user = User::create([
            'name' => $principal->full_name,
            'email' => $principal->email,
            'phone_number' => $principal->phone_number,
            'tenant_application_id' => $tenantApplication->id,
            'password' => bcrypt($password),
        ]);

        $user->assignRole('tenant');

        // Send congratulatory email with credentials
        Mail::to($principal->email)->send(new ContractSignedMail($principal->full_name, $principal->email, $password));

        // Create the RentContract record
        $contract =RentContract::create([
            'property_id' => $request->property_id,
            'tenant_application_id' => $tenantApplication->id,
            'user_id' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'monthly_amount' => $request->monthly_amount,
            'deposit_amount' => $request->deposit_amount,
            'contract_doc' => $contractUrl,
            'status' => 'En curso',
        ]);

        // 🔁 Load property and user info (only specific fields)
        $contract->load([
            'property:id,sku',
            'user:id,name'
        ]);


        $billType = BillType::firstOrCreate(
            [
                'rent_contract_id' => $contract->id,
                'name' => 'Arriendo',
            ]
        );

        // Create the bill
        Bill::create([
            'bill_type_id' => $billType->id,
            'start_date' => $request->start_date,
            'end_date' => Carbon::parse($request->start_date)->copy()->addMonth(),
            'due_date' => $request->start_date,
            'amount' => $contract->monthly_amount,
            'status' => 'Pendiente',
            'property_id' => $contract->property_id,
            'user_id' => $contract->user_id
        ]);

        // 🔄 Reject other pending applications for this property
        $otherApplications = TenantApplication::with('financialResponsibles')
            ->where('property_id', $request->property_id)
            ->whereIn('status', ['Pendiente', 'Guardado'])
            ->where('id', '!=', $tenantApplication->id)
            ->get();

        foreach ($otherApplications as $otherApp) {
            $otherApp->status = $otherApp->status == "Pendiente" ? "Pendiente Rechazado" : "Guardado Rechazado";
            // $otherApp->status = 'Pendiente Rechazado';
            $otherApp->save();

            $otherPrincipal = $otherApp->financialResponsibles->where('principal', 1)->first();

            if ($otherPrincipal) {
                Mail::to($otherPrincipal->email)->send(new TenantApplicationDenied($otherPrincipal->full_name));
            }
        }

        return response()->json(['success' => true, "data" => $contract]);
    }


    public function getContracts()
    {
        $contracts = RentContract::with([
            'property:id,sku',
            'user:id,name'
        ])->get();

        return response()->json(['success' => true, "data" => $contracts]);
    }

    public function terminateContract(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rent_contracts,id',
        ]);

        $contract = RentContract::with(["tenantApplication"])->findOrFail($request->id);
        $contract->tenantApplication->status = 'Terminado';
        $contract->status = 'Terminado';
        $contract->end_date = now();
        $contract->save();

        return response()->json([
            'success' => true,
        ]);
    }



}


