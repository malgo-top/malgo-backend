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
        Schema::create('electricity_bills', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2);
            $table->decimal('aseo', 10, 2);
            $table->decimal('heads_201', 10, 2);
            $table->decimal('heads_301', 10, 2);
            $table->decimal('heads_401', 10, 2);
            $table->string("bill_doc")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_bills');
    }
};
