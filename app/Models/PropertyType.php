<?php

// app/Models/PropertyType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $fillable = ['name', 'requires_deposit'];

    public function properties() {
        return $this->hasMany(Property::class);
    }
}
