<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\PaymentHasBill;
use App\Models\Bill;
use App\Mail\PaymentVerified;

class PaymentController extends Controller
{

    public function getPayments(){

        $user = auth()->user();

        $payments = Payment::with([
            'bills' => function ($billQuery) {
                $billQuery->with([
                    'property:id,sku',
                    'billType:id,name'
                ]);
            }
        ]);

        // If user is NOT admin, filter by user ID
        if (!$user->hasRole('admin')) {
            $payments->whereHas('bills', function ($billQuery) use ($user) {
                $billQuery->where('user_id', $user->id);
            });
        }

        $data = $payments->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function verifyPayment(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::with('bills.user')->findOrFail($request->id);

        $payment->status = 'Confirmado';
        $payment->verified_at = now();
        $payment->save();

        // Get the user via the first related bill (assuming one user per payment)
        $user = $payment->bills->first()->user ?? null;

        if ($user && $user->email) {
            Mail::to($user->email)->send(new PaymentVerified($user));
        }

        return response()->json([
            'success' => true,
        ]);
    }




}
