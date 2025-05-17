<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantApplication extends Model
{
    protected $fillable = ['property_id', 'move_in_date', 'status', 'comment'];

    // Relationships
    public function financialResponsibles() {
        return $this->hasMany(FinancialResponsible::class);
    }

    public function cohabitants() {
        return $this->hasMany(Cohabitant::class);
    }

    public function pets() {
        return $this->hasMany(Pet::class);
    }

    public function parkingNeeds() {
        return $this->hasMany(ParkingNeed::class);
    }

    public function property() {
        return $this->belongsTo(Property::class);
    }
}
