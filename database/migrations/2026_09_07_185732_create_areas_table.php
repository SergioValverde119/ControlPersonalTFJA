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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained('salas')->cascadeOnDelete();
            $table->string('clave', 40)->unique(); // Ej: 'SRM_01_P1', 'SRM_01_OFICIALIA'
            $table->string('nombre', 150);         // Ej: 'Primera Ponencia', 'Oficialía de Partes Común'
            $table->string('tipo', 50);            // 'PONENCIA', 'SECRETARIA_ACUERDOS', 'ACTUARIA', 'OFICIALIA_PARTES', 'PRESIDENCIA'
            $table->unsignedTinyInteger('numero')->nullable(); // 1, 2, 3 (solo aplica si tipo es PONENCIA)
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
