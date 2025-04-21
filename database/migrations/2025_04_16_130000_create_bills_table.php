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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_type_id')->constrained('bill_types');
            $table->string('start_date');
            $table->string('end_date');
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('user_id')->constrained('users');
            $table->string('due_date');
            $table->boolean("late_notice_sent")->default(false);
            $table->string('amount');
            $table->string('status')->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
