<?php

namespace App\Actions\Workflow;

use App\Models\CatalogoEstatusTramite;
use App\Models\CatalogoTipoDocumento;
use App\Models\Persona;
use App\Models\Tramite;
use App\Models\TramiteDocumento;
use App\Models\User;
use App\Services\Workflow\GeneradorCadenaFirmas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrarAltaTramiteAction
{
    public function __construct(
        protected GeneradorCadenaFirmas $generadorFirmas
    ) {}

    /**
     * Ejecuta el registro de la propuesta ágil (Fase 1).
     *
     * @param  array  $datos           Datos validados del StoreAltaTramiteRequest
     * @param  UploadedFile  $oficio   Archivo PDF del oficio de propuesta
     * @param  UploadedFile  $cv       Archivo PDF del currículum vitae
     * @param  User  $solicitante      Usuario autenticado que captura la propuesta
     */
    public function ejecutar(
        array $datos,
        UploadedFile $oficio,
        UploadedFile $cv,
        User $solicitante
    ): Tramite {
        return DB::transaction(function () use ($datos, $oficio, $cv, $solicitante) {
            // 1. UPSERT EN IDENTIDAD CIVIL (PERSONAS)
            $persona = Persona::updateOrCreate(
                ['curp' => $datos['curp']],
                [
                    'rfc'               => $datos['rfc'],
                    'nombre'            => $datos['nombre'],
                    'primer_apellido'   => $datos['primer_apellido'],
                    'segundo_apellido'  => $datos['segundo_apellido'] ?? null,
                    'correo_contacto'   => $datos['correo_contacto'] ?? null,
                    'telefono_contacto' => $datos['telefono_contacto'] ?? null,
                    // Si ya existía, conserva su estado; si no, nace como no activo
                    'es_empleado_activo'=> DB::raw('COALESCE(es_empleado_activo, false)'),
                ]
            );

            // Refrescar para asegurar tipos casteados
            $persona->refresh();

            // 2. CREAR REGISTRO CENTRAL DEL TRÁMITE
            $estatusInicial = CatalogoEstatusTramite::where('clave', 'BORRADOR')->firstOrFail();

            $tramite = Tramite::create([
                'folio'                   => $this->generarFolioUnico(),
                'tipo_movimiento'         => 'ALTA',
                'persona_id'              => $persona->id,
                'plaza_destino_id'        => $datos['plaza_destino_id'],
                'usuario_solicitante_id'  => $solicitante->id,
                'estatus_id'              => $estatusInicial->id,
                'fecha_efectos_propuesta' => $datos['fecha_efectos_propuesta'],
            ]);

            // 3. CARGA DOCUMENTAL LIGERA (tramite_documentos)
            $this->almacenarDocumentoTransaccional($tramite, $oficio, 'OFICIO_PROPUESTA');
            $this->almacenarDocumentoTransaccional($tramite, $cv, 'CURRICULUM_VITAE');

            // 4. DETONAR ESCALERA JERÁRQUICA DE FIRMAS
            $this->generadorFirmas->procesar($tramite);

            return $tramite->fresh(['estatus', 'firmas.firmante', 'plazaDestino.puesto']);
        });
    }

    /**
     * Guarda el archivo físico y registra la metadata en tramite_documentos.
     */
    protected function almacenarDocumentoTransaccional(
        Tramite $tramite,
        UploadedFile $archivo,
        string $claveTipoDocumento
    ): TramiteDocumento {
        $tipoDoc = CatalogoTipoDocumento::firstOrCreate(
            ['clave' => $claveTipoDocumento],
            [
                'nombre'         => Str::headline($claveTipoDocumento),
                'alcance'        => 'NUEVO_INGRESO',
                'es_obligatorio' => true,
                'activo'         => true,
            ]
        );

        $directorio = "tramites/{$tramite->id}";
        $nombreHash = Str::uuid() . '.' . $archivo->getClientOriginalExtension();
        $rutaFisica = $archivo->storeAs($directorio, $nombreHash, 'local');

        return TramiteDocumento::create([
            'tramite_id'        => $tramite->id,
            'tipo_documento_id' => $tipoDoc->id,
            'ruta_archivo'      => $rutaFisica,
            'nombre_original'   => $archivo->getClientOriginalName(),
            'tamano_bytes'      => $archivo->getSize(),
            'mime_type'         => $archivo->getClientMimeType(),
        ]);
    }

    /**
     * Genera un folio institucional secuencial con formato TFJA/ALTA/YYYY/XXXXXX.
     */
    protected function generarFolioUnico(): string
    {
        $año = now()->year;
        $consecutivo = Tramite::whereYear('created_at', $año)->count() + 1;

        return sprintf('TFJA/ALTA/%d/%06d', $año, $consecutivo);
    }
}
