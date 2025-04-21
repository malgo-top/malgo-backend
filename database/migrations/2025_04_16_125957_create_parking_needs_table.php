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
        Schema::create('parking_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_application_id')->constrained('tenant_applications');
            $table->string('vehicle_type'); // Car/Motorcycle
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_needs');
    }
};
