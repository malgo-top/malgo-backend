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
        Schema::create('cohabitants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_application_id')->constrained('tenant_applications');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('document_number');
            $table->string('occupation');
            $table->string('age');
            $table->string('relationship');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohabitants');
    }
};
