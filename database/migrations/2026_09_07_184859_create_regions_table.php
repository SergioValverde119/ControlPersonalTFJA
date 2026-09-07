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
        Schema::create('regiones', function (Blueprint $table) {
    $table->id();
    $table->string('clave', 20)->unique();   // Ej: 'METROPOLITANA', 'NOROESTE'
    $table->string('nombre', 150);           // Ej: 'Región Metropolitana'
    $table->string('sede', 100)->nullable(); // Ej: 'Ciudad de México'
    $table->boolean('activo')->default(true)->index();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
