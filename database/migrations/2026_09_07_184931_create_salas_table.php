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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regiones')->cascadeOnDelete();
            $table->string('clave', 30)->unique();            // Ej: 'SRM_01'
            $table->string('nombre', 200);                    // Ej: 'Primera Sala Regional Metropolitana'
            $table->string('tipo', 50)->default('ORDINARIA'); // ORDINARIA, ESPECIALIZADA, AUXILIAR
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};
