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
        Schema::create('rent_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tenant_application_id')->constrained('tenant_applications');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('monthly_amount');
            $table->string('deposit_amount');
            $table->string('contract_doc');
            $table->string('status')->default('En curso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_contracts');
    }
};
