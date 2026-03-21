<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');          // Ej: "Martes de 2x1"
            $table->text('descripcion')->nullable(); // Ej: "En todas las clásicas..."
            $table->integer('porcentaje');     // Ej: 50 (para 50% off) o 0 si es precio fijo
            $table->decimal('precio_promo', 8, 2)->nullable(); // Ej: $99.00
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('imagen_url')->nullable(); // Para el banner
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
