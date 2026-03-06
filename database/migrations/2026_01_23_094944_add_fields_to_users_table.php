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
        Schema::table('users', function (Blueprint $table) {
            // Telefono: Importante que sea nullable al inicio para no romper usuarios viejos
            $table->string('telefono', 20)->nullable()->after('email');
            
            // Avatar: URL de la imagen (puede ser null)
            $table->string('avatar')->nullable()->after('rol');
            
            // Estado: Por defecto el usuario está activo (true/1)
            $table->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'avatar', 'is_active']);
        });
    }
};
