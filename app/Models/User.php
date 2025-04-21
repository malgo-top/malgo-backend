<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use ESolution\DBEncryption\Traits\EncryptedAttribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, EncryptedAttribute;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_application_id',
        'email',
        'phone_number'
    ];

    protected $encryptable = ['name', "phone_number"];

    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class, 'tenant_application_id');
    }

    public function rentContracts() {

        return $this->hasMany(RentContract::class);
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
