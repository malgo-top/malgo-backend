<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantApplication;
use App\Models\Bill;
use App\Models\RentContract;
use App\Models\BillType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\LateBillNotice;

class dailyChecking extends Command
{
    protected $signature = 'malgo:daily-checks';
    protected $description = 'Process tenant application cleanup, overdue bills, and contract billing.';

    public function handle()
    {
        $this->markOverdueBills();
        $this->deleteOldRejectedTenantApplications();
        $this->generateUpcomingRentBills();
        $this->notifyLateBills();

        $this->info('Daily billing tasks completed successfully.');
    }

    protected function deleteOldRejectedTenantApplications()
    {
        $threshold = Carbon::now()->subDays(30);
        $tenantApplication = TenantApplication::where('status', 'Pendiente Rechazado')
            ->where('updated_at', '<', $threshold)
            ->get();

        // 2. Delete files from AWS S3
        foreach ($tenantApplication->financialResponsibles as $responsible) {
            // Delete document files from responsible
            $fileFields = [
                'document_id',
                'document_certf',
                'document_pay_1',
                'document_pay_2',
                'document_pay_3',
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

        $this->info("Deleted old rejected tenant applications.");
    }

    protected function deleteS3FileByUrl($url)
    {
        // $parsed = parse_url($url);
        // $path = ltrim($parsed['path'], '/');

        if (Storage::disk('s3')->exists($url)) {
            Storage::disk('s3')->delete($url);
        }
    }

    protected function markOverdueBills()
    {
        $today = Carbon::today();

        $bills = Bill::where('due_date',  $today)
            ->where('status', 'Pendiente')
            ->get();

        foreach ($bills as $bill) {
            $bill->status = 'Atrasado';
            $bill->save();
        }

        $this->info("Marked Pendiente bills as 'Atrasado'.");
    }


    protected function notifyLateBills()
    {
        $today = Carbon::today();
        $admin = User::find(1);

        $bills = Bill::with(['user', 'billType']) // eager load billType
            ->where('late_notice_sent', false)
            ->where('status', "Atrasado")
            ->where(function ($query) use ($today) {
                $query->whereHas('billType', function ($q) {
                    $q->whereNull('rent_contract_id');
                })->whereDate('due_date', $today) // Notify on due_date
                ->orWhereHas('billType', function ($q) {
                    $q->whereNotNull('rent_contract_id');
                })->whereDate('due_date', $today->copy()->subDays(3)); // Notify 3 days after due_date
            })
            ->get();

        foreach ($bills as $bill) {
            if ($bill->user) {
                Mail::to($bill->user->email)
                    ->cc($admin->email)
                    ->send(new LateBillNotice($bill->user->name, $bill));

                $bill->late_notice_sent = true;
                $bill->save();

                $this->info("Late notice sent to {$bill->user->email} and copied to admin.");
            }
        }
    }

    protected function generateUpcomingRentBills()
    {
        $today = Carbon::today();

        $contracts = RentContract::with('property')
            ->where('status', 'En curso')
            ->get();

        foreach ($contracts as $contract) {
            $startDate = Carbon::parse($contract->start_date);

            // Generate up to 12 months into the contract
            for ($i = 0; $i < 12; $i++) {

                $billStartDate = $startDate->copy()->addMonths($i); // e.g. Apr 25
                $billEndDate = $billStartDate->copy()->addMonth();  // e.g. May 25
                $dueDate = $billStartDate->copy(); // due on same day as rental start
                $generateOn = $dueDate->copy()->subDays(15); // e.g. Apr 10

                if ($generateOn->isSameDay($today)) {
                    // Check if bill already exists for this due date + contract
                    $exists = Bill::whereHas('billType', function ($q) use ($contract) {
                        $q->where('rent_contract_id', $contract->id);
                    })->whereDate('due_date', $dueDate)->exists();

                    if ($exists) {
                        $this->info("⚠️ Bill already exists for due date {$dueDate->toDateString()} (contract ID {$contract->id})");
                        break;
                    }

                    $billType = BillType::firstOrCreate(
                        [
                            'rent_contract_id' => $contract->id,
                            'name' => 'Arriendo',
                        ]
                    );

                    // Create the bill
                    Bill::create([
                        'bill_type_id' => $billType->id,
                        'start_date' => $billStartDate,
                        'end_date' => $billEndDate,
                        'due_date' => $dueDate,
                        'amount' => $contract->monthly_amount,
                        'status' => 'Pendiente',
                        'property_id' => $contract->property_id,
                        'user_id' => $contract->user_id
                    ]);

                    $this->info("✅ Created advance rent bill for contract ID {$contract->id} | Period: {$billStartDate->format('d/m')} - {$billEndDate->format('d/m')} | Due: {$dueDate->format('d/m')}");
                    break;
                }
            }
        }
    }



}
