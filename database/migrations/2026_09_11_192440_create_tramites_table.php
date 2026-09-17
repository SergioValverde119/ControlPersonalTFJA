<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_tramites_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 30)->unique();
            $table->enum('tipo_movimiento', [
                'ALTA_NUEVO_INGRESO', 
                'PROMOCION', 
                'DEMOCION', 
                'CAMBIO_ADSCRIPCION', 
                'REINGRESO'
            ]);
            $table->foreignId('persona_id')->constrained('personas');
            $table->foreignId('plaza_destino_id')->constrained('plazas');
            $table->foreignId('plaza_origen_id')->nullable()->constrained('plazas');
            $table->foreignId('usuario_solicitante_id')->constrained('users');
            $table->foreignId('estatus_id')->constrained('catalogo_estatus_tramites')->restrictOnDelete();
            $table->foreignId('corte_jga_id')->nullable()->constrained('cortes_jga')->nullOnDelete();
            $table->date('fecha_efectos_propuesta');
            $table->timestamps();

            $table->index(['estatus_id', 'corte_jga_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};