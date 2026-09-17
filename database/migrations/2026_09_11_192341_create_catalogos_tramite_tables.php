<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_estatus_tramites', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->string('badge_color', 30)->default('gray');
            $table->unsignedTinyInteger('orden_flujo')->default(1);
            $table->boolean('permite_edicion')->default(false);
            $table->boolean('es_terminal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('catalogo_tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 150);
            $table->enum('alcance', ['PERSONA', 'TRAMITE']);
            $table->boolean('es_obligatorio')->default(true);
            $table->boolean('requiere_vigencia')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('cortes_jga', function (Blueprint $table) {
            $table->id();
            $table->string('numero_corte', 50)->unique();
            $table->date('fecha_cierre');
            $table->date('fecha_sesion_jga');
            $table->enum('estatus', ['ABIERTO', 'EN_REVISION_JGA', 'SESIONADO'])->default('ABIERTO');
            $table->string('concentrado_excel_path')->nullable();
            $table->string('concentrado_pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cortes_jga');
        Schema::dropIfExists('catalogo_tipos_documento');
        Schema::dropIfExists('catalogo_estatus_tramites');
    }
};
