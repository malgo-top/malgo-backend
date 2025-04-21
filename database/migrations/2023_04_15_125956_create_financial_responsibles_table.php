<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/[...]_create_financial_responsibles_table.php
    public function up() {
        Schema::create('financial_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_application_id')->constrained('tenant_applications');
            $table->string('full_name');
            $table->string('document_type');
            $table->string('document_number');
            $table->string('phone_number');
            $table->string('email');
            $table->date('birthdate');
            $table->string('nationality');
            $table->boolean('principal')->default('0'); // Principal/Co-signer

            $table->string('document_id');
            $table->string('document_certf'); // Certificación Laboral/Cámara
            $table->string('document_pay_1'); // Desprendible/Extracto 1
            $table->string('document_pay_2'); // Desprendible/Extracto 2
            $table->string('document_pay_3'); // Desprendible/Extracto 3
            $table->string('employment_status'); // Employed/Independent
            $table->string('monthly_salary');
            $table->date('start_current_job_date');

            $table->string('guarantor_full_name')->nullable();
            $table->string('guarantor_document_type')->nullable();
            $table->string('guarantor_document_number')->nullable();
            $table->string('guarantor_property_cert')->nullable(); // PDF path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_responsibles');
    }
};
