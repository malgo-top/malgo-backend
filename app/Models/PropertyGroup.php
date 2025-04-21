<?php

// app/Models/PropertyGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyGroup extends Model
{
    protected $fillable = ['name'];

    public function properties() {
        return $this->hasMany(Property::class);
    }
}
