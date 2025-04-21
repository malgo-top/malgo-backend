<?php

// app/Models/Cohabitant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;


class Cohabitant extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'tenant_application_id', 'first_name', 'last_name',
        'document_number', 'occupation', 'age', 'relationship'
    ];

    protected $encryptable = [
        'first_name', 'last_name', 'document_number',
        'occupation', 'age', 'relationship'
    ];

    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class);
    }
}
