<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 150);
            $table->string('nivel_tabular', 10);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('plazas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_plaza', 50)->unique();
            $table->foreignId('unidad_organizacional_id')->constrained('unidades_organizacionales');
            $table->foreignId('puesto_id')->constrained('puestos');
            $table->enum('estatus', ['VACANTE', 'RESERVADA_TRAMITE', 'OCUPADA', 'CONGELADA'])->default('VACANTE');
            $table->date('disponible_desde')->nullable();
            $table->timestamps();

            $table->index(['unidad_organizacional_id', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plazas');
        Schema::dropIfExists('puestos');
    }
};