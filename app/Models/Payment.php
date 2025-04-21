<?php
// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;


class Payment extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'amount', 'payment_method', 'status', 'paid_at', 'verified_at', "payment_cert"
    ];

    protected $encryptable = [
        'amount', 'payment_method', 'status', 'paid_at', 'verified_at', "payment_cert"
    ];

    public function bills() {
        return $this->belongsToMany(Bill::class, 'payment_has_bills');
    }

    protected $appends = [
        'payment_cert_url',
    ];

    public function getPaymentCertUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->payment_cert);
    }

    private function generateTemporaryUrl($path)
    {
        if (!$path) return null;

        $cleanPath = ltrim(preg_replace('/\/+/', '/', $path), '/');

        return Storage::disk('s3')->temporaryUrl(
            $path,
            now()->addMinutes(30)
        );
    }

}
