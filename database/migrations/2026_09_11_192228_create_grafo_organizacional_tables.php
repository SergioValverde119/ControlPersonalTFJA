<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpieza de tablas rígidas anteriores si existen
        Schema::dropIfExists('areas');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('regiones');

        Schema::create('tipos_unidad', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 100);
            $table->boolean('escala_inmediato')->default(false);
            $table->timestamps();
        });

        Schema::create('unidades_organizacionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_unidad_id')->constrained('tipos_unidad');
            $table->foreignId('padre_id')->nullable()->constrained('unidades_organizacionales')->nullOnDelete();
            $table->string('path', 255)->index();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 150);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('titularidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_organizacional_id')->constrained('unidades_organizacionales')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo', ['TITULAR', 'ENCARGADO_DESPACHO', 'SUPLENTE'])->default('TITULAR');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['unidad_organizacional_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titularidades');
        Schema::dropIfExists('unidades_organizacionales');
        Schema::dropIfExists('tipos_unidad');
    }
};