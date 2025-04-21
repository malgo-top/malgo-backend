<?php
// app/Models/Property.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;

class Property extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'address', 'property_type_id', 'property_group_id', "sku", "accepting_applications"
    ];

    protected $encryptable = [
        'address', "sku"
    ];


    public function type() {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function group() {
        return $this->belongsTo(PropertyGroup::class, 'property_group_id');
    }

    public function tenant() {
        return $this->belongsTo(User::class, 'occupied_by');
    }
}
