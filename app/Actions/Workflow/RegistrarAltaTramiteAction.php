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
use Illuminate\Support\Str;

class RegistrarAltaTramiteAction
{
    public function __construct(
        protected GeneradorCadenaFirmas $generadorFirmas
    ) {}

    /**
     * Ejecuta el registro de la propuesta ágil (Fase 1: Solicitante).
     *
     * @param  array  $datos           Datos validados por StoreAltaTramiteRequest
     * @param  UploadedFile  $oficio   PDF del oficio de propuesta
     * @param  UploadedFile  $cv       PDF del currículum vitae
     * @param  User  $solicitante      Usuario autenticado que captura
     */
    public function ejecutar(
        array $datos,
        UploadedFile $oficio,
        UploadedFile $cv,
        User $solicitante
    ): Tramite {
        return DB::transaction(function () use ($datos, $oficio, $cv, $solicitante) {
            // 1. Upsert en PERSONAS
            $persona = Persona::updateOrCreate(
                ['curp' => $datos['curp']],
                [
                    'rfc'              => $datos['rfc'],
                    'nombre'           => $datos['nombre'],
                    'primer_apellido'  => $datos['primer_apellido'],
                    'segundo_apellido' => $datos['segundo_apellido'] ?? null,
                    'telefono'         => $datos['telefono_contacto'] ?? null,
                ]
            );

            // 2. Crear el registro principal del TRAMITE con el enum exacto
            $estatusInicial = CatalogoEstatusTramite::where('clave', 'BORRADOR')->firstOrFail();

            $tramite = Tramite::create([
                'folio'                   => $this->generarFolioUnico(),
                'tipo_movimiento'         => 'ALTA_NUEVO_INGRESO',
                'persona_id'              => $persona->id,
                'plaza_destino_id'        => $datos['plaza_destino_id'],
                'usuario_solicitante_id'  => $solicitante->id,
                'estatus_id'              => $estatusInicial->id,
                'fecha_efectos_propuesta' => $datos['fecha_efectos_propuesta'],
            ]);

            // 3. Carga documental ligera en tramite_documentos (archivo_path + hash_sha256)
            $this->almacenarDocumentoTransaccional($tramite, $oficio, 'OFICIO_PROPUESTA');
            $this->almacenarDocumentoTransaccional($tramite, $cv, 'CURRICULUM_VITAE');

            // 4. Detonar la cadena jerárquica de firmas
            $this->generadorFirmas->procesar($tramite);

            return $tramite->fresh([
                'estatus',
                'firmas.firmante',
                'plazaDestino.puesto',
                'documentos.tipoDocumento',
            ]);
        });
    }

    /**
     * Almacena físicamente el PDF en disco local y genera su registro con hash SHA-256.
     */
    protected function almacenarDocumentoTransaccional(
        Tramite $tramite,
        UploadedFile $archivo,
        string $claveTipoDocumento
    ): TramiteDocumento {
        $tipoDoc = CatalogoTipoDocumento::firstOrCreate(
            ['clave' => $claveTipoDocumento],
            [
                'nombre'            => Str::headline($claveTipoDocumento),
                'alcance'           => 'TRAMITE',
                'es_obligatorio'    => true,
                'requiere_vigencia' => false,
                'activo'            => true,
            ]
        );

        $directorio = "tramites/{$tramite->id}";
        $nombreHash = Str::uuid() . '.' . $archivo->getClientOriginalExtension();
        $rutaFisica = $archivo->storeAs($directorio, $nombreHash, 'local');

        return TramiteDocumento::create([
            'tramite_id'        => $tramite->id,
            'tipo_documento_id' => $tipoDoc->id,
            'archivo_path'      => $rutaFisica,
            'nombre_original'   => $archivo->getClientOriginalName(),
            'hash_sha256'       => hash_file('sha256', $archivo->getRealPath()),
        ]);
    }

    /**
     * Genera el consecutivo institucional TFJA/ALTA/YYYY/XXXXXX.
     */
    protected function generarFolioUnico(): string
    {
        $año = now()->year;
        $consecutivo = Tramite::whereYear('created_at', $año)->count() + 1;

        return sprintf('TFJA/ALTA/%d/%06d', $año, $consecutivo);
    }
}
