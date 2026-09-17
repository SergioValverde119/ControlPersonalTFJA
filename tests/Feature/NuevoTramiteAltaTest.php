<?php

namespace Tests\Feature;

use App\Models\CatalogoEstatusTramite;
use App\Models\CatalogoTipoDocumento;
use App\Models\Persona;
use App\Models\Plaza;
use App\Models\Puesto;
use App\Models\TipoUnidad;
use App\Models\Titularidad;
use App\Models\Tramite;
use App\Models\UnidadOrganizacional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NuevoTramiteAltaTest extends TestCase
{
    use RefreshDatabase;

    protected Puesto $puesto;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        // 1. Catálogos de estatus
        CatalogoEstatusTramite::firstOrCreate(['clave' => 'BORRADOR'], ['nombre' => 'Borrador']);
        CatalogoEstatusTramite::firstOrCreate(['clave' => 'AUTORIZACION_JERARQUICA'], ['nombre' => 'En Autorización Jerárquica']);
        CatalogoEstatusTramite::firstOrCreate(['clave' => 'MESA_TECNICA_RH'], ['nombre' => 'En Mesa Técnica RH']);
        CatalogoEstatusTramite::firstOrCreate(['clave' => 'RECHAZADO'], ['nombre' => 'Rechazado']);

        // 2. Catálogos de documentos (alcance: TRAMITE)
        CatalogoTipoDocumento::firstOrCreate(
            ['clave' => 'OFICIO_PROPUESTA'],
            [
                'nombre'            => 'Oficio de Propuesta',
                'alcance'           => 'TRAMITE',
                'es_obligatorio'    => true,
                'requiere_vigencia' => false,
                'activo'            => true,
            ]
        );

        CatalogoTipoDocumento::firstOrCreate(
            ['clave' => 'CURRICULUM_VITAE'],
            [
                'nombre'            => 'Currículum Vitae',
                'alcance'           => 'TRAMITE',
                'es_obligatorio'    => true,
                'requiere_vigencia' => false,
                'activo'            => true,
            ]
        );

        // 3. Puesto base
        $this->puesto = Puesto::firstOrCreate(
            ['clave' => 'ACT-JUR'],
            ['nombre' => 'Actuario Judicial', 'nivel_tabular' => '24']
        );
    }

    public function test_solicitante_puede_registrar_propuesta_agil_y_se_genera_escalera_de_firmas(): void
    {
        $tipoSede = TipoUnidad::firstOrCreate(
            ['clave' => 'SEDE'],
            ['nombre' => 'Sala Regional Sede', 'escala_inmediato' => false]
        );

        $unidadSede = UnidadOrganizacional::create([
            'clave'          => 'SR-METRO',
            'nombre'         => 'Sala Regional Metropolitana',
            'tipo_unidad_id' => $tipoSede->id,
            'path'           => '/1/',
            'activo'         => true,
        ]);

        $unidadPonencia = UnidadOrganizacional::create([
            'clave'          => 'SR-P1',
            'nombre'         => 'Primera Ponencia',
            'tipo_unidad_id' => $tipoSede->id,
            'padre_id'       => $unidadSede->id,
            'path'           => "/{$unidadSede->id}/2/",
            'activo'         => true,
        ]);

        // Titularidades con fecha_inicio
        $magistradoPresidente = User::factory()->create(['name' => 'Presidente Sala']);
        Titularidad::create([
            'unidad_organizacional_id' => $unidadSede->id,
            'user_id'                  => $magistradoPresidente->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        $magistradoPonente = User::factory()->create(['name' => 'Magistrado Solicitante']);
        Titularidad::create([
            'unidad_organizacional_id' => $unidadPonencia->id,
            'user_id'                  => $magistradoPonente->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        // Plaza vacante adscrita a la Ponencia del solicitante
        $plaza = Plaza::create([
            'codigo_plaza'             => 'PLZ-001',
            'unidad_organizacional_id' => $unidadPonencia->id,
            'puesto_id'                => $this->puesto->id,
            'estatus'                  => 'VACANTE',
            'disponible_desde'         => now()->subDay(),
        ]);

        // Petición HTTP: Captura Ligera
        $payload = [
            'plaza_destino_id'        => $plaza->id,
            'fecha_efectos_propuesta' => now()->addMonth()->format('Y-m-d'),
            'curp'                    => 'PEGA850315HDFRRN01',
            'rfc'                     => 'PEGA850315AB1',
            'nombre'                  => 'Gabriel',
            'primer_apellido'         => 'Perez',
            'segundo_apellido'        => 'Garcia',
            'correo_contacto'         => 'gabriel.perez@ejemplo.com',
            'oficio_propuesta'        => UploadedFile::fake()->create('oficio_propuesta.pdf', 300, 'application/pdf'),
            'curriculum_vitae'        => UploadedFile::fake()->create('cv_candidato.pdf', 400, 'application/pdf'),
        ];

        $response = $this->actingAs($magistradoPonente)
            ->post(route('nuevo-tramite.alta.store'), $payload);

        $response->assertSessionHasNoErrors();

        // Aserciones
        $tramite = Tramite::where('plaza_destino_id', $plaza->id)->first();
        $this->assertNotNull($tramite);

        $response->assertRedirect(route('estado-tramite.show', $tramite));

        $this->assertDatabaseHas('personas', [
            'curp' => 'PEGA850315HDFRRN01',
            'rfc'  => 'PEGA850315AB1',
        ]);

        $this->assertCount(2, $tramite->documentos);
        foreach ($tramite->documentos as $doc) {
            Storage::disk('local')->assertExists($doc->archivo_path);
            $this->assertNotEmpty($doc->hash_sha256);
        }

        // Escalera de firmas: omite autofirma y turna al superior
        $this->assertDatabaseHas('tramite_firmas', [
            'tramite_id'       => $tramite->id,
            'firmante_user_id' => $magistradoPresidente->id,
            'etiqueta_rol'     => 'PRESIDENTE_SALA',
            'orden'            => 1,
            'estatus'          => 'PENDIENTE',
        ]);

        $this->assertEquals('AUTORIZACION_JERARQUICA', $tramite->fresh()->estatus->clave);
    }

    public function test_falla_validacion_si_solicitante_propone_plaza_ajena_a_su_oficina(): void
    {
        $tipoPonencia = TipoUnidad::firstOrCreate(
            ['clave' => 'PONENCIA'],
            ['nombre' => 'Ponencia', 'escala_inmediato' => false]
        );

        $unidadPropia = UnidadOrganizacional::create([
            'clave'          => 'UNI-A',
            'nombre'         => 'Unidad Solicitante',
            'tipo_unidad_id' => $tipoPonencia->id,
            'path'           => '/1/',
            'activo'         => true,
        ]);

        $unidadAjena = UnidadOrganizacional::create([
            'clave'          => 'UNI-B',
            'nombre'         => 'Unidad Otra Sala',
            'tipo_unidad_id' => $tipoPonencia->id,
            'path'           => '/2/',
            'activo'         => true,
        ]);

        $usuario = User::factory()->create();
        Titularidad::create([
            'unidad_organizacional_id' => $unidadPropia->id,
            'user_id'                  => $usuario->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        $plazaAjena = Plaza::create([
            'codigo_plaza'             => 'PLZ-AJENA',
            'unidad_organizacional_id' => $unidadAjena->id,
            'puesto_id'                => $this->puesto->id,
            'estatus'                  => 'VACANTE',
        ]);

        $response = $this->actingAs($usuario)
            ->from(route('nuevo-tramite.alta'))
            ->post(route('nuevo-tramite.alta.store'), [
                'plaza_destino_id'        => $plazaAjena->id,
                'fecha_efectos_propuesta' => now()->addMonth()->format('Y-m-d'),
                'curp'                    => 'PEGA850315HDFRRN01',
                'rfc'                     => 'PEGA850315AB1',
                'nombre'                  => 'Gabriel',
                'primer_apellido'         => 'Perez',
                'oficio_propuesta'        => UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'),
                'curriculum_vitae'        => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
            ]);

        $response->assertSessionHasErrors('plaza_destino_id');
        $this->assertEquals(0, Tramite::count());
    }

    public function test_genera_candado_de_venia_previo_a_jerarquia_si_aspirante_es_empleado_activo(): void
    {
        $this->withoutExceptionHandling();
        $tipoSede = TipoUnidad::firstOrCreate(
            ['clave' => 'SEDE'],
            ['nombre' => 'Sala Sede', 'escala_inmediato' => false]
        );

        $unidadOrigen = UnidadOrganizacional::create([
            'clave'          => 'ORIGEN',
            'nombre'         => 'Sala Cedente',
            'tipo_unidad_id' => $tipoSede->id,
            'path'           => '/1/',
            'activo'         => true,
        ]);

        $unidadDestino = UnidadOrganizacional::create([
            'clave'          => 'DESTINO',
            'nombre'         => 'Sala Receptora',
            'tipo_unidad_id' => $tipoSede->id,
            'path'           => '/2/',
            'activo'         => true,
        ]);

        $titularCedente = User::factory()->create(['name' => 'Magistrado Cedente']);
        Titularidad::create([
            'unidad_organizacional_id' => $unidadOrigen->id,
            'user_id'                  => $titularCedente->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        $solicitante = User::factory()->create(['name' => 'Magistrado Receptor']);
        Titularidad::create([
            'unidad_organizacional_id' => $unidadDestino->id,
            'user_id'                  => $solicitante->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        // Aspirante con adscripción previa en unidad cedente
        $persona = Persona::create([
            'curp'            => 'ACTI850101HDFRRN09',
            'rfc'             => 'ACTI850101AB1',
            'nombre'          => 'EMPLEADO',
            'primer_apellido' => 'ACTIVO',
            'telefono'        => '5512345678',
        ]);

        $usuarioEmpleado = User::factory()->create([
            'persona_id' => $persona->id,
        ]);

        Titularidad::create([
            'unidad_organizacional_id' => $unidadOrigen->id,
            'user_id'                  => $usuarioEmpleado->id,
            'activo'                   => true,
            'fecha_inicio'             => now()->subMonth(),
        ]);

        $plazaDestino = Plaza::create([
            'codigo_plaza'             => 'PLZ-NUEVA',
            'unidad_organizacional_id' => $unidadDestino->id,
            'puesto_id'                => $this->puesto->id,
            'estatus'                  => 'VACANTE',
        ]);

        $response = $this->actingAs($solicitante)->post(route('nuevo-tramite.alta.store'), [
            'plaza_destino_id'        => $plazaDestino->id,
            'fecha_efectos_propuesta' => now()->addMonth()->format('Y-m-d'),
            'curp'                    => $persona->curp,
            'rfc'                     => $persona->rfc,
            'nombre'                  => $persona->nombre,
            'primer_apellido'         => $persona->primer_apellido,
            'oficio_propuesta'        => UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'),
            'curriculum_vitae'        => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasNoErrors();

        $tramite = Tramite::where('plaza_destino_id', $plazaDestino->id)->first();
        $this->assertNotNull($tramite);

        // Turno 1 debe ser la VENIA del titular cedente
        $primeraFirma = $tramite->firmas()->orderBy('orden')->first();

        $this->assertNotNull($primeraFirma);
        $this->assertEquals(1, $primeraFirma->orden);
        $this->assertEquals('TITULAR_SUPERIOR', $primeraFirma->etiqueta_rol);
        $this->assertEquals($titularCedente->id, $primeraFirma->firmante_user_id);
    }
}
