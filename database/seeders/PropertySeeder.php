<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // List of SKUs
        $skus = [
            "SE-APTO-201", "SE-APTO-202", "SE-APTO-301", "SE-APTO-302", "SE-APTO-401",
            "SE-APTO-402", "SE-APTO-501", "SE-APTO-502", "SE-PARQ-A1", "SE-PARQ-A2",
            "SE-PARQ-B1", "SE-PARQ-B2", "SE-PARQ-C1", "SE-PARQ-C2", "SE-PARQ-D1",
            "SE-PARQ-D2", "SE-LOC-1", "FO-APTO-201", "FO-APTO-301", "FO-PARQ-1",
            "FO-PARQ-2", "PE-APTO-101", "PE-APTO-102", "PE-APTO-103"
        ];

        // Create a Property instance for each SKU
        foreach ($skus as $sku) {
            Property::create([
                'sku' => $sku,
                'accepting_applications' => (bool) random_int(0, 1),  // Random true/false
                'address' => null, // Leave empty
                'property_type_id' => null, // Leave empty
                'property_group_id' => null, // Leave empty
            ]);
        }
    }
}
