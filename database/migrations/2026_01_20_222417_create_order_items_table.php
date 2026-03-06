<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // CORRECCIÓN IMPORTANTE: 
            // Como tu tabla 'products' tiene la llave 'id_producto', debemos especificarlo aquí:
            $table->foreignId('product_id')->constrained('products', 'id_producto'); 
            
            // Detalles de la Venta
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); 
            
            // Personalización (Sin cebolla, etc)
            $table->json('opciones')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};