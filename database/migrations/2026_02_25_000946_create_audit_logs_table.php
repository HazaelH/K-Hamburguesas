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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name'); // Quien autorizó
            $table->string('empleado_name'); // Quien lo solicitó
            $table->string('accion'); // Ej: "Aprobó", "Rechazó"
            $table->text('detalle'); // Ej: "Baja del producto: Hamburguesa Doble"
            $table->timestamps(); // Guardará la fecha y hora exacta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
