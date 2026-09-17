<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('catalogo_tipos_documento');
            $table->string('archivo_path', 255);
            $table->string('hash_sha256', 64);
            $table->string('descripcion_adicional', 150)->nullable();
            $table->date('vigencia_fin')->nullable();
            $table->boolean('validado_por_rh')->default(false);
            $table->timestamps();

            $table->index(['persona_id', 'tipo_documento_id']);
        });

        Schema::create('persona_contactos_emergencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->enum('parentesco', ['CONYUGE', 'HIJO', 'PADRE', 'MADRE', 'HERMANO', 'OTRO']);
            $table->string('nombre_completo', 150);
            $table->string('telefono', 20);
            $table->string('telefono_alterno', 20)->nullable();
            $table->string('direccion_completa', 255);
            $table->boolean('es_beneficiario')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_contactos_emergencia');
        Schema::dropIfExists('persona_documentos');
    }
};