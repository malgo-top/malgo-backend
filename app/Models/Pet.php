<?php

// app/Models/Pet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['tenant_application_id', 'type', 'sex'];

    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class);
    }
}
