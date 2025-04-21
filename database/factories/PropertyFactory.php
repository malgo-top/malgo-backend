<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'address' => null, // Leave empty
            'property_type_id' => null, // Leave empty
            'property_group_id' => null, // Leave empty
            'accepting_applications' => $this->faker->boolean, // Random true/false
        ];
    }
}
