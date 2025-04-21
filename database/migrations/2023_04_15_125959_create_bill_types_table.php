<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('bill_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Rent, Water, etc.
            $table->foreignId('rent_contract_id')->nullable()->constrained('rent_contracts');
            $table->foreignId('water_bill_id')->nullable()->constrained('water_bills');
            $table->foreignId('gas_bill_id')->nullable()->constrained('gas_bills');
            $table->foreignId('electricity_bill_id')->nullable()->constrained('electricity_bills');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_types');
    }
};
