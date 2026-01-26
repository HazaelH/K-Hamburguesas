<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // ID del pedido
            
            // Relación con Usuario
            $table->foreignId('user_id')->constrained('users');
            
            // Datos Financieros
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago')->default('tarjeta');
            $table->string('payment_id')->nullable(); 
            
            // Estado del Pedido
            $table->enum('status', ['pendiente', 'pagado', 'cocinando', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
            
            // Datos de Entrega (Snapshot en JSON)
            $table->json('datos_entrega')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};