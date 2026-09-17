<?php

namespace Database\Seeders;

// database/seeders/GrafoDemoSeeder.php
namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Plaza;
use App\Models\Puesto;
use App\Models\Role;
use App\Models\TipoUnidad;
use App\Models\Titularidad;
use App\Models\UnidadOrganizacional;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GrafoDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Consultar Catálogos Base (Sin duplicar roles)
        $tRaiz = TipoUnidad::where('clave', 'RAIZ')->firstOrFail();
        $tTerritorio = TipoUnidad::where('clave', 'TERRITORIO')->firstOrFail();
        $tSede = TipoUnidad::where('clave', 'SEDE')->firstOrFail();
        $tPonencia = TipoUnidad::where('clave', 'PONENCIA')->firstOrFail();
        $tCompartida = TipoUnidad::where('clave', 'SOPORTE_COMPARTIDO')->firstOrFail();

        $rolPresidente = Role::where('clave', 'MAGISTRADO_PRESIDENTE')->first();
        $rolPonente = Role::where('clave', 'MAGISTRADO_PONENTE')->first();
        $rolVisitador = Role::where('clave', 'MAGISTRADO_VISITADOR')->first();
        $rolRh = Role::where('clave', 'RECURSOS_HUMANOS')->first();

        // 2. Nodos Institucionales (Nombres agnósticos de cargos temporales)
        $tfja = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'TFJA-CENTRAL'],
            ['tipo_unidad_id' => $tRaiz->id, 'padre_id' => null, 'path' => '/1/', 'nombre' => 'Tribunal Federal de Justicia Administrativa']
        );

        $region = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'REG-METRO'],
            ['tipo_unidad_id' => $tTerritorio->id, 'padre_id' => $tfja->id, 'path' => "/{$tfja->id}/2/", 'nombre' => 'Región Centro Metropolitana']
        );

        $sala1 = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'SR-METRO-01'],
            ['tipo_unidad_id' => $tSede->id, 'padre_id' => $region->id, 'path' => "{$region->path}3/", 'nombre' => 'Primera Sala Regional Metropolitana']
        );

        // Nombres formales de ponencias: sin quemar al "Presidente" en el texto
        $ponencia1 = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'SR-METRO-01-P1'],
            ['tipo_unidad_id' => $tPonencia->id, 'padre_id' => $sala1->id, 'path' => "{$sala1->path}4/", 'nombre' => 'Primera Ponencia']
        );

        $ponencia2 = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'SR-METRO-01-P2'],
            ['tipo_unidad_id' => $tPonencia->id, 'padre_id' => $sala1->id, 'path' => "{$sala1->path}5/", 'nombre' => 'Segunda Ponencia']
        );

        $ponencia3 = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'SR-METRO-01-P3'],
            ['tipo_unidad_id' => $tPonencia->id, 'padre_id' => $sala1->id, 'path' => "{$sala1->path}6/", 'nombre' => 'Tercera Ponencia']
        );

        $oficPartes = UnidadOrganizacional::updateOrCreate(
            ['clave' => 'SR-METRO-01-OP'],
            ['tipo_unidad_id' => $tCompartida->id, 'padre_id' => $sala1->id, 'path' => "{$sala1->path}7/", 'nombre' => 'Oficialía de Partes Común']
        );

        // 3. Servidores Públicos de Prueba
        // Caso Magistrada Presidenta: Titular de Primera Ponencia Y Titular de la Presidencia de Sala (Sede)
        $pMag1 = Persona::updateOrCreate(
            ['curp' => 'PREP750202MDFR02'],
            ['rfc' => 'PREP750202BB2', 'nombre' => 'Claudia', 'primer_apellido' => 'Paredes', 'segundo_apellido' => 'Luna']
        );
        $uMag1 = User::updateOrCreate(
            ['email' => 'claudia.paredes@tfja.gob.mx'],
            ['name' => 'Magda. Claudia Paredes Luna', 'password' => Hash::make('password123'), 'persona_id' => $pMag1->id, 'activo' => true]
        );
        if ($rolPresidente && $rolPonente) {
            $uMag1->roles()->syncWithoutDetaching([$rolPresidente->id, $rolPonente->id]);
        }

        // Doble titularidad: es la titular de su Ponencia y ostenta la Presidencia de la Sede este periodo
        Titularidad::updateOrCreate(
            ['unidad_organizacional_id' => $ponencia1->id, 'user_id' => $uMag1->id],
            ['tipo' => 'TITULAR', 'fecha_inicio' => '2026-01-01', 'activo' => true]
        );
        Titularidad::updateOrCreate(
            ['unidad_organizacional_id' => $sala1->id, 'user_id' => $uMag1->id],
            ['tipo' => 'TITULAR', 'fecha_inicio' => '2026-01-01', 'activo' => true]
        );

        // Caso Magistrado Ponente 2
        $pMag2 = Persona::updateOrCreate(
            ['curp' => 'PONG800303HDFR03'],
            ['rfc' => 'PONG800303CC3', 'nombre' => 'Fernando', 'primer_apellido' => 'Garza', 'segundo_apellido' => 'Ríos']
        );
        $uMag2 = User::updateOrCreate(
            ['email' => 'fernando.garza@tfja.gob.mx'],
            ['name' => 'Mag. Fernando Garza Ríos', 'password' => Hash::make('password123'), 'persona_id' => $pMag2->id, 'activo' => true]
        );
        if ($rolPonente) {
            $uMag2->roles()->syncWithoutDetaching([$rolPonente->id]);
        }
        Titularidad::updateOrCreate(
            ['unidad_organizacional_id' => $ponencia2->id, 'user_id' => $uMag2->id],
            ['tipo' => 'TITULAR', 'fecha_inicio' => '2026-01-01', 'activo' => true]
        );

        // Caso Magistrado Visitador Territorial
        $pVis = Persona::updateOrCreate(
            ['curp' => 'VISM700101HDFR01'],
            ['rfc' => 'VISM700101AA1', 'nombre' => 'Roberto', 'primer_apellido' => 'Mendoza', 'segundo_apellido' => 'Soto']
        );
        $uVis = User::updateOrCreate(
            ['email' => 'roberto.mendoza@tfja.gob.mx'],
            ['name' => 'Mag. Roberto Mendoza Soto', 'password' => Hash::make('password123'), 'persona_id' => $pVis->id, 'activo' => true]
        );
        if ($rolVisitador) {
            $uVis->roles()->syncWithoutDetaching([$rolVisitador->id]);
        }
        Titularidad::updateOrCreate(
            ['unidad_organizacional_id' => $region->id, 'user_id' => $uVis->id],
            ['tipo' => 'TITULAR', 'fecha_inicio' => '2026-01-01', 'activo' => true]
        );

        // Caso Analista de Mesa Técnica de RH
        $pRh = Persona::updateOrCreate(
            ['curp' => 'RHAN850404MDFR04'],
            ['rfc' => 'RHAN850404DD4', 'nombre' => 'Adriana', 'primer_apellido' => 'Navarro', 'segundo_apellido' => 'Cruz']
        );
        $uRh = User::updateOrCreate(
            ['email' => 'adriana.navarro@tfja.gob.mx'],
            ['name' => 'Lic. Adriana Navarro Cruz', 'password' => Hash::make('password123'), 'persona_id' => $pRh->id, 'activo' => true]
        );
        if ($rolRh) {
            $uRh->roles()->syncWithoutDetaching([$rolRh->id]);
        }

        // 4. Plazas de prueba en Segunda Ponencia
        $puestoSec = Puesto::firstOrCreate(
            ['clave' => 'SEC_ACUERDOS'],
            ['nombre' => 'Secretario de Acuerdos de Sala Regional', 'nivel_tabular' => 'OA1']
        );

        Plaza::updateOrCreate(
            ['codigo_plaza' => 'PLZ-SR1-P2-001'],
            [
                'unidad_organizacional_id' => $ponencia2->id,
                'puesto_id' => $puestoSec->id,
                'estatus' => 'VACANTE',
                'disponible_desde' => '2026-01-15',
            ]
        );
    }
}