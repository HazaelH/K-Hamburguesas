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
        Schema::create('products', function (Blueprint $table) {
            // Identificadores
            $table->id('id_producto'); 
            
            // Datos Básicos
            $table->string('nombre');
            $table->string('slug')->unique()->nullable(); 
            $table->decimal('precio', 8, 2);
            $table->text('descripcion')->nullable();
            $table->string('imagen_url')->nullable();
            
            // Categorización
            $table->string('categoria')->index(); 
            
            // Configuración Compleja
            $table->json('opciones_personalizacion')->nullable(); 
            
            // Control de Estado
            $table->boolean('is_active')->default(true); // Baja lógica
            $table->boolean('is_available')->default(true); // Stock diario
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};