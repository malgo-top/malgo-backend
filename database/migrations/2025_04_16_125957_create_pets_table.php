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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_application_id')->constrained('tenant_applications');
            $table->string('type'); // Dog/Cat/etc.
            $table->string('sex'); // Male/Female
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
