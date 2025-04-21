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
        Schema::create('additional_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_responsible_id')->constrained('financial_responsibles');
            $table->string('monthly_amount');
            $table->text('description');
            $table->string('income_cert');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_incomes');
    }
};
