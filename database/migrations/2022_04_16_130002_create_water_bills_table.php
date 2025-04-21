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
        Schema::create('water_bills', function (Blueprint $table) {
            $table->id();
            $table->decimal('metros_cubicos_201', 10, 2);
            $table->decimal('metros_cubicos_301', 10, 2);
            $table->decimal('acueducto_cargo_fijo', 10, 2);
            $table->decimal('acueducto_consumo_residencial_basico_quantity', 10, 2);
            $table->decimal('acueducto_consumo_residencial_basico_subtotal', 10, 2);
            $table->decimal('acueducto_consumo_residencial_superior_quantity', 10, 2);
            $table->decimal('acueducto_consumo_residencial_superior_subtotal', 10, 2);
            $table->decimal('alcantarillado_cargo_fijo', 10, 2);
            $table->decimal('alcantarillado_consumo_residencial_basico_quantity', 10, 2);
            $table->decimal('alcantarillado_consumo_residencial_basico_subtotal', 10, 2);
            $table->decimal('alcantarillado_consumo_residencial_superior_quantity', 10, 2);
            $table->decimal('alcantarillado_consumo_residencial_superior_subtotal', 10, 2);
            $table->string("bill_doc")->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_bills');
    }
};
