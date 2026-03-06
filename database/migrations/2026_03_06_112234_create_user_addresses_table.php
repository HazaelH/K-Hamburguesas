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
    Schema::create('user_addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // El "Alias" de la dirección (Ej: "Casa", "Oficina")
        $table->string('alias')->default('Mi Dirección'); 
        
        // Datos de contacto para esta dirección específica (con espacio para la extensión +52 etc)
        $table->string('codigo_pais', 10)->default('+52');
        $table->string('telefono', 20);
        
        // Datos del domicilio
        $table->string('calle');
        $table->string('numero', 20);
        $table->string('codigo_postal', 10);
        $table->string('colonia');
        $table->string('municipio')->nullable();
        $table->string('estado')->nullable();
        $table->text('referencias')->nullable();
        
        // Saber si es la dirección favorita/principal
        $table->boolean('is_default')->default(false); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
