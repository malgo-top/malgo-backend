<?php

// app/Models/RentContract.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;


class RentContract extends Model
{
    use EncryptedAttribute;
    
    protected $fillable = [
        'property_id', "tenant_application_id", 'user_id', 'start_date', 'end_date',
        'monthly_amount', 'deposit_amount', "contract_doc", 'status'
    ];

    protected $encryptable = [
        'start_date', 'end_date',
        'monthly_amount', 'deposit_amount', "contract_doc",
    ];


    public function property() {
        return $this->belongsTo(Property::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class);
    }

    protected $appends = [
        'contract_doc_url',
    ];

    public function getContractDocUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->contract_doc);
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
