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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('address')->nullable();
            // $table->decimal('monthly_price', 10, 2);
            // $table->boolean('is_occupied')->default(false);
            // $table->foreignId('occupied_by')->nullable()->constrained('users');
            // $table->date('occupied_from')->nullable();
            $table->boolean("accepting_applications")->default(false);
            $table->foreignId('property_type_id')->nullable()->constrained('property_types');
            $table->foreignId('property_group_id')->nullable()->constrained('property_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
