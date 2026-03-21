<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Ej: "Hamburguesas" (Debe coincidir exacto con lo que hay en products)
            $table->string('nombre_en')->nullable(); // Ej: "Burgers"
            $table->string('nombre_pt')->nullable(); // Ej: "Hambúrgueres"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
