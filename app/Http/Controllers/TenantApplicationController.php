<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\TenantApplication;
use App\Models\FinancialResponsible;
use App\Models\AdditionalIncome;
use App\Models\Cohabitant;
use App\Models\Pet;
use App\Models\ParkingNeed;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantApplicationDenied;
use App\Mail\TenantApplicationAccepted;
use App\Mail\NewApplication;
use App\Mail\DocumentAsk;
use App\Mail\NewDocument;

class TenantApplicationController extends Controller
{

    public function createTenantApplication(Request $request)
    {

           try {
        $property = Property::whereEncrypted('sku', $request->property_id)->firstOrFail();

        // 1. Create the main tenant application
        $application = TenantApplication::create([
            'property_id' => $property->id,
            'move_in_date' => $request->move_in_date,
            'status' => 'Pendiente' // You can adjust this default status
        ]);

        // 2. Handle financial responsibles
        foreach ($request->FinancialResponsible as $index => $responsibleData) {
            $financialResponsible = new FinancialResponsible();
            $financialResponsible->fill([
                'tenant_application_id' => $application->id,
                'full_name' => $responsibleData['full_name'],
                'email' => $responsibleData['email'],
                'phone_number' => $responsibleData['phone_number'],
                'document_type' => $responsibleData['document_type'],
                'document_number' => $responsibleData['document_number'],
                'birthdate' => $responsibleData['birthdate'],
                'nationality' => $responsibleData['nationality'],
                'employment_status' => $responsibleData['employment_status'],
                'monthly_salary' => $responsibleData['monthly_salary'],
                'business_description' => $responsibleData['business_description'] ? $responsibleData['business_description'] : "No aplica",
                'start_current_job_date' => $responsibleData['start_current_job_date'],
                'guarantor_full_name' => $responsibleData['guarantor_full_name'],
                'guarantor_document_type' => $responsibleData['guarantor_document_type'],
                'guarantor_document_number' => $responsibleData['guarantor_document_number'],
                'principal' => $index === 0 ? 1 : 0,
            ]);


            // Handle file uploads to S3
            $fileFields = [
                'document_id',
                'document_certf',
                'document_pay_1',
                'document_pay_2',
                'document_pay_3',
                'document_other',
                'guarantor_property_cert'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile("FinancialResponsible.$index.$field")) {
                    $path = $request->file("FinancialResponsible.$index.$field")->store('tenant_files', 's3');
                    $financialResponsible->$field = $path;
                    // $financialResponsible->$field = Storage::disk('s3')->url($path);
                }
            }

            $financialResponsible->save();

            // Handle additional income
            if (isset($responsibleData['AdditionalIncome'])) {
                foreach ($responsibleData['AdditionalIncome'] as $incomeIndex => $income) {
                    $additionalIncome = new AdditionalIncome();
                    $additionalIncome->fill([
                        'financial_responsible_id' => $financialResponsible->id,
                        'monthly_amount' => $income['monthly_amount'],
                        'description' => $income['description'],
                    ]);

                    if ($request->hasFile("FinancialResponsible.$index.AdditionalIncome.$incomeIndex.income_cert")) {
                        $incomePath = $request->file("FinancialResponsible.$index.AdditionalIncome.$incomeIndex.income_cert")->store('tenant_files', 's3');
                        $additionalIncome->income_cert = $incomePath;
                        // $additionalIncome->income_cert = Storage::disk('s3')->url($incomePath);
                    }

                    $additionalIncome->save();
                }
            }
        }

        // 3. Handle cohabitants
        if ($request->has('Cohabitant')) {
            foreach ($request->Cohabitant as $cohabitantData) {
                Cohabitant::create([
                    'tenant_application_id' => $application->id,
                    'first_name' => $cohabitantData['first_name'],
                    'last_name' => $cohabitantData['last_name'],
                    'document_number' => $cohabitantData['document_number'],
                    'occupation' => $cohabitantData['occupation'],
                    'age' => $cohabitantData['age'],
                    'relationship' => $cohabitantData['relationship'],
                ]);
            }
        }

        // 4. Handle pets
        if ($request->has('Pet')) {
            foreach ($request->Pet as $petData) {
                Pet::create([
                    'tenant_application_id' => $application->id,
                    'type' => $petData['type'],
                    'sex' => $petData['sex'],
                    'size' => $petData['size'],
                ]);
            }
        }

        // 5. Handle parking needs
        if ($request->has('ParkingNeed')) {
            foreach ($request->ParkingNeed as $parkingData) {
                ParkingNeed::create([
                    'tenant_application_id' => $application->id,
                    'vehicle_type' => $parkingData['vehicle_type'],
                ]);
            }
        }

        $emailAdmin = User::findOrFail(1); 
        Mail::to($emailAdmin->email)->send(new NewApplication($request->property_id));

        return response()->json([
            'success' => true
        ]);

           } catch (\Exception $e) {
            return $e;
        }

    }

    public function getTenantApplications(){

        $application = TenantApplication::with([
            'property:id,sku',
            'financialResponsibles.additionalIncomes',
            'cohabitants',
            'pets',
            'parkingNeeds'
        ])->get();

        return response()->json(["success" => true, "data" => $application]);

    }

    public function denyTenantApplication(Request $request)
    {
        $request->validate([
            'tenantID' => 'required|exists:tenant_applications,id',
        ]);

        $tenantApplication = TenantApplication::with([
            'financialResponsibles.additionalIncomes',
            'cohabitants',
            'pets',
            'parkingNeeds'
        ])->findOrFail($request->tenantID);

        // 1. Email the principal financial responsible
        $principal = $tenantApplication->financialResponsibles->where('principal', 1)->first();

        if ($principal && $principal->email) {
            Mail::to($principal->email)->send(new TenantApplicationDenied($principal->full_name));
        }

        // 2. Delete files from AWS S3
        foreach ($tenantApplication->financialResponsibles as $responsible) {
            // Delete document files from responsible
            $fileFields = [
                'document_id',
                'document_certf',
                'document_pay_1',
                'document_pay_2',
                'document_pay_3',
                'document_other',
                'document_asked',
                'guarantor_property_cert'
            ];

            foreach ($fileFields as $field) {
                if (!empty($responsible->$field)) {
                    $this->deleteS3FileByUrl($responsible->$field);
                }
            }

            // Delete AdditionalIncome cert files
            foreach ($responsible->additionalIncomes as $income) {
                if (!empty($income->income_cert)) {
                    $this->deleteS3FileByUrl($income->income_cert);
                }
                $income->delete(); // Delete record
            }

            $responsible->delete(); // Delete financial responsible
        }

        // 3. Delete related records
        $tenantApplication->cohabitants()->delete();
        $tenantApplication->pets()->delete();
        $tenantApplication->parkingNeeds()->delete();

        // 4. Delete the tenant application
        $tenantApplication->delete();

        $application = TenantApplication::with([
            'property:id,sku',
            'financialResponsibles.additionalIncomes',
            'cohabitants',
            'pets',
            'parkingNeeds'
        ])->get();

        return response()->json(["success" => true, "data" => $application]);
    }

    protected function deleteS3FileByUrl($url)
    {

        // $parsed = parse_url($url);
        // $path = ltrim($parsed['path'], '/');

        // if (Storage::disk('s3')->exists($path)) {
        //     Storage::disk('s3')->delete($path);
        // }

        Storage::disk('s3')->delete($url);

    }

    public function acceptTenantApplication(Request $request)
    {
        $request->validate([
            'tenantID' => 'required|exists:tenant_applications,id',
        ]);

        $tenantApplication = TenantApplication::with('financialResponsibles')->findOrFail($request->tenantID);

        // Get the principal responsible person
        $principal = $tenantApplication->financialResponsibles->where('principal', 1)->first();

        if ($principal && $principal->email) {
            Mail::to($principal->email)->send(new TenantApplicationAccepted($principal->full_name));
        }

        // Update the status
        $tenantApplication->status = 'Pendiente Contrato';
        $tenantApplication->save();

        $application = TenantApplication::with([
            'property:id,sku',
            'financialResponsibles.additionalIncomes',
            'cohabitants',
            'pets',
            'parkingNeeds'
        ])->get();

        return response()->json(["success" => true, "data" => $application]);

    }

    public function saveTenantApplication(Request $request)
    {
        $request->validate([
            'tenantID' => 'required|exists:tenant_applications,id',
        ]);

        $tenantApplication = TenantApplication::with('financialResponsibles')->findOrFail($request->tenantID);

        $tenantApplication->status = 'Guardado';
        $tenantApplication->save();

        $application = TenantApplication::with([
            'property:id,sku',
            'financialResponsibles.additionalIncomes',
            'cohabitants',
            'pets',
            'parkingNeeds'
        ])->get();

        return response()->json(["success" => true, "data" => $application]);

    }

    public function updateComment(Request $request){
        
        $ta = TenantApplication::findOrFail($request->id);
        $ta->comment = $request->comment;
        $ta->save();

        return response()->json(["success" => true]);

    }

    // public function requestOtherDoc(Request $request)
    // {
    //     $ta = TenantApplication::findOrFail($request->id);
    
    //     $code = $ta->id * 3141; 
    //     $tenApId = $request->tenApId;

    //     $link = "https://www.malgo.top/#/document/asked?tenApId=" . urlencode($tenApId) . "&code=" . urlencode($code);

    //     Mail::to($request->email)->send(new DocumentAsk($request->name, $link, $request->message));

    //     return response()->json(["success" => true]);
    // }

    
    public function requestOtherDoc(Request $request)
    {
        $code = $request->id * 3141; 
        $tenApId = $request->id;

        $link = "https://www.malgo.top/#/document/asked?tenApId=" . urlencode($tenApId) . "&code=" . urlencode($code);

        Mail::to($request->email)->send(new DocumentAsk($request->name, $link, $request->message));

        return response()->json(["success" => true]);
    }

    public function uploadOtherDoc(Request $request){

        $fr = FinancialResponsible::where("tenant_application_id",$request->input('tenApId'))->where("principal", 1)->first();

        if($fr->document_asked){
            return response()->json(["success" => false]);
        }

        $path = $request->file('document_asked')->store('tenant_files', 's3');

        $fr->document_asked = $path;
        $fr->save();

        try {
            $emailAdmin = User::findOrFail(1); 
            Mail::to($emailAdmin->email)->send(new NewDocument($request->input('tenApId')));
        } catch (\Exception $e) {
        }

        return response()->json(["success" => true]);

    }


}
