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
        Schema::table('orders', function (Blueprint $table) {
            
            // 1. MESA (Nueva)
            if (!Schema::hasColumn('orders', 'mesa')) {
                $table->string('mesa')->nullable()->after('id'); 
            }

            // 2. TIPO DE SERVICIO (Nuevo)
            if (!Schema::hasColumn('orders', 'tipo_servicio')) {
                $table->string('tipo_servicio')->default('delivery')->after('mesa');
            }

            // 3. USER ID (Nuevo - Empleado)
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }

            // 4. DIRECCION (El que daba error)
            if (Schema::hasColumn('orders', 'direccion')) {
                // Si existe, la hacemos opcional
                $table->string('direccion')->nullable()->change();
            } else {
                // Si NO existe, la creamos desde cero
                $table->string('direccion')->nullable();
            }

            // 5. TELEFONO
            if (Schema::hasColumn('orders', 'telefono')) {
                $table->string('telefono')->nullable()->change();
            } else {
                $table->string('telefono')->nullable();
            }
            
            // 6. CLIENTE NOMBRE
            if (Schema::hasColumn('orders', 'cliente_nombre')) {
                $table->string('cliente_nombre')->nullable()->change();
            } else {
                $table->string('cliente_nombre')->nullable();
            }
        });
    }

    public function down()
    {
        // Revertir cambios si fuera necesario
        Schema::table('orders', function (Blueprint $table) {
            $table->string('direccion')->nullable(false)->change();
            // ... (resto de reversiones)
        });
    }
};
