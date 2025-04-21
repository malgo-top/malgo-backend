<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // [...]_create_property_types_table.php
    public function up() {
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Apartment, Parking, etc.
            $table->boolean('requires_deposit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_types');
    }
};
