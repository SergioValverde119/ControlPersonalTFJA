<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramite_antecedentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->enum('tipo_validacion', ['VENIA_ADSCRIPCION', 'RECOMENDACION_REINGRESO']);
            $table->foreignId('unidad_cedente_id')->nullable()->constrained('unidades_organizacionales');
            $table->foreignId('titular_evaluador_user_id')->nullable()->constrained('users');
            $table->enum('resultado', ['PENDIENTE', 'OTORGADO', 'NEGADO'])->default('PENDIENTE');
            $table->string('oficio_delegado_path', 255)->nullable();
            $table->text('comentarios_recomendacion')->nullable();
            $table->timestamp('respondido_en')->nullable();
            $table->timestamps();
        });

        Schema::create('tramite_firmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->unsignedTinyInteger('orden');
            $table->enum('etiqueta_rol', ['TITULAR_SUPERIOR', 'PRESIDENTE_SALA', 'MAGISTRADO_VISITADOR', 'SOA']);
            $table->foreignId('unidad_organizacional_id')->constrained('unidades_organizacionales');
            $table->foreignId('firmante_user_id')->constrained('users');
            $table->enum('estatus', ['PENDIENTE', 'APROBADO', 'DEVUELTO_OBSERVADO'])->default('PENDIENTE');
            $table->text('motivo_devolucion')->nullable();
            $table->timestamp('firmado_en')->nullable();
            $table->timestamps();

            $table->unique(['tramite_id', 'orden']);
        });

        Schema::create('tramite_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('catalogo_tipos_documento');
            $table->string('archivo_path', 255);
            $table->string('nombre_original', 255);
            $table->string('hash_sha256', 64);
            $table->timestamps();
        });

        Schema::create('tramite_revision_rubros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->foreignId('revisor_user_id')->constrained('users');
            $table->enum('carril_origen', ['JERARQUICO', 'REGIONAL', 'RECURSOS_HUMANOS']);
            $table->enum('tipo_rubro', ['DATO_FORMULARIO', 'DOCUMENTO_PDF']);
            $table->string('clave_rubro', 50);
            $table->foreignId('tramite_documento_id')->nullable()->constrained('tramite_documentos')->cascadeOnDelete();
            $table->boolean('es_valido')->default(true);
            $table->text('observacion')->nullable();
            $table->boolean('subsanado')->default(false);
            $table->timestamp('subsanado_en')->nullable();
            $table->timestamps();

            $table->index(['tramite_id', 'es_valido', 'subsanado']);
        });

        Schema::create('tramite_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->foreignId('evaluador_user_id')->constrained('users');
            $table->enum('tipo_evaluacion', ['PSICOMETRICO', 'TECNICO_JURISDICCIONAL', 'TECNICO_ADMINISTRATIVO']);
            $table->decimal('puntaje', 5, 2);
            $table->enum('resultado', ['APROBADO', 'NO_APROBADO', 'CONDICIONADO']);
            $table->string('reporte_archivo_path', 255);
            $table->text('observaciones')->nullable();
            $table->timestamp('evaluado_en');
            $table->timestamps();
        });

        Schema::create('tramite_resoluciones_jga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->unique()->constrained('tramites')->cascadeOnDelete();
            $table->foreignId('corte_jga_id')->constrained('cortes_jga');
            $table->enum('sentido', ['AUTORIZADO', 'DESECHADO']);
            $table->string('numero_acuerdo', 100)->nullable();
            $table->date('fecha_acuerdo')->nullable();
            $table->string('oficio_resolucion_path', 255)->nullable();
            $table->boolean('sincronizado_sigpagk')->default(false);
            $table->timestamp('sincronizado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramite_resoluciones_jga');
        Schema::dropIfExists('tramite_evaluaciones');
        Schema::dropIfExists('tramite_revision_rubros');
        Schema::dropIfExists('tramite_documentos');
        Schema::dropIfExists('tramite_firmas');
        Schema::dropIfExists('tramite_antecedentes');
    }
};