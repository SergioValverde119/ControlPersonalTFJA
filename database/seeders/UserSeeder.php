<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Region;
use App\Models\Role;
use App\Models\Sala;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. ÚNICAMENTE los 5 Roles Institucionales Acordados
            $roles = [
                'ADMIN_DGTIC'           => Role::firstOrCreate(['clave' => 'ADMIN_DGTIC'], ['nombre' => 'Administrador DGTIC']),
                'RECURSOS_HUMANOS'      => Role::firstOrCreate(['clave' => 'RECURSOS_HUMANOS'], ['nombre' => 'Recursos Humanos']),
                'MAGISTRADO_PRESIDENTE' => Role::firstOrCreate(['clave' => 'MAGISTRADO_PRESIDENTE'], ['nombre' => 'Magistrado Presidente']),
                'MAGISTRADO_PONENTE'    => Role::firstOrCreate(['clave' => 'MAGISTRADO_PONENTE'], ['nombre' => 'Magistrado Ponente']),
                'MAGISTRADO_VISITADOR'  => Role::firstOrCreate(['clave' => 'MAGISTRADO_VISITADOR'], ['nombre' => 'Magistrado Visitador']),
            ];

            // 2. Estructura Territorial (2 Salas con 2 Ponencias cada una)
            $regionMetro = Region::firstOrCreate(
                ['clave' => 'REG-CDMX'],
                ['nombre' => 'Región Metropolitana (CDMX)', 'activo' => true]
            );

            // Sala 1
            $sala1 = Sala::firstOrCreate(
                ['clave' => 'SALA-METRO-01'],
                ['nombre' => 'Primera Sala Regional Metropolitana', 'tipo' => 'REGIONAL', 'region_id' => $regionMetro->id, 'activo' => true]
            );

            $ponencia1S1 = Area::firstOrCreate(
                ['clave' => 'PON-01-S1'],
                ['nombre' => 'Primera Ponencia - Sala 1', 'tipo' => 'PONENCIA', 'numero' => 1, 'sala_id' => $sala1->id, 'activo' => true]
            );

            $ponencia2S1 = Area::firstOrCreate(
                ['clave' => 'PON-02-S1'],
                ['nombre' => 'Segunda Ponencia - Sala 1', 'tipo' => 'PONENCIA', 'numero' => 2, 'sala_id' => $sala1->id, 'activo' => true]
            );

            // Sala 2
            $sala2 = Sala::firstOrCreate(
                ['clave' => 'SALA-METRO-02'],
                ['nombre' => 'Segunda Sala Regional Metropolitana', 'tipo' => 'REGIONAL', 'region_id' => $regionMetro->id, 'activo' => true]
            );

            $ponencia1S2 = Area::firstOrCreate(
                ['clave' => 'PON-01-S2'],
                ['nombre' => 'Primera Ponencia - Sala 2', 'tipo' => 'PONENCIA', 'numero' => 1, 'sala_id' => $sala2->id, 'activo' => true]
            );

            $ponencia2S2 = Area::firstOrCreate(
                ['clave' => 'PON-02-S2'],
                ['nombre' => 'Segunda Ponencia - Sala 2', 'tipo' => 'PONENCIA', 'numero' => 2, 'sala_id' => $sala2->id, 'activo' => true]
            );

            $passwordGenerica = 'Password123!';

            // 3. Padrón de 14 Usuarios usando EXCLUSIVAMENTE los 5 roles
            $usuariosPrueba = [
                // ADMIN_DGTIC (2 registros)
                [
                    'name'      => 'Ing. Administrador Titular DGTIC',
                    'email'     => 'admin.titular@tfja.gob.mx',
                    'rol'       => 'ADMIN_DGTIC',
                    'region_id' => null,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Ing. Administrador Soporte DGTIC',
                    'email'     => 'admin.soporte@tfja.gob.mx',
                    'rol'       => 'ADMIN_DGTIC',
                    'region_id' => null,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],

                // RECURSOS_HUMANOS (3 registros)
                [
                    'name'      => 'Lic. Director de Recursos Humanos',
                    'email'     => 'rh.director@tfja.gob.mx',
                    'rol'       => 'RECURSOS_HUMANOS',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Lic. Analista de Nóminas y Plantillas',
                    'email'     => 'rh.analista@tfja.gob.mx',
                    'rol'       => 'RECURSOS_HUMANOS',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Lic. Auxiliar de Validación RH',
                    'email'     => 'rh.auxiliar@tfja.gob.mx',
                    'rol'       => 'RECURSOS_HUMANOS',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],

                // MAGISTRADO_VISITADOR (2 registros)
                [
                    'name'      => 'Mag. Visitador Regional Primera Zona',
                    'email'     => 'visitador.zona1@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_VISITADOR',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Mag. Visitador Regional Segunda Zona',
                    'email'     => 'visitador.zona2@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_VISITADOR',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => null,
                    'area_id'   => null,
                    'activo'    => true,
                ],

                // MAGISTRADO_PRESIDENTE (2 registros: uno por cada sala)
                [
                    'name'      => 'Mag. Presidente Primera Sala Regional',
                    'email'     => 'presidente.sala1@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PRESIDENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala1->id,
                    'area_id'   => null,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Mag. Presidente Segunda Sala Regional',
                    'email'     => 'presidente.sala2@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PRESIDENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala2->id,
                    'area_id'   => null,
                    'activo'    => true,
                ],

                // MAGISTRADO_PONENTE (4 registros: distribuidos en ponencias de Sala 1 y Sala 2)
                [
                    'name'      => 'Mag. Ponente Sala 1 - Ponencia 1',
                    'email'     => 'ponente1.sala1@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PONENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala1->id,
                    'area_id'   => $ponencia1S1->id,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Mag. Ponente Sala 1 - Ponencia 2',
                    'email'     => 'ponente2.sala1@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PONENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala1->id,
                    'area_id'   => $ponencia2S1->id,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Mag. Ponente Sala 2 - Ponencia 1',
                    'email'     => 'ponente1.sala2@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PONENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala2->id,
                    'area_id'   => $ponencia1S2->id,
                    'activo'    => true,
                ],
                [
                    'name'      => 'Mag. Ponente Sala 2 - Ponencia 2',
                    'email'     => 'ponente2.sala2@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PONENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala2->id,
                    'area_id'   => $ponencia2S2->id,
                    'activo'    => true,
                ],

                // REGISTRO INACTIVO (Para probar filtro de estatus)
                [
                    'name'      => 'Mag. Ponente con Licencia (Inactivo)',
                    'email'     => 'ponente.inactivo@tfja.gob.mx',
                    'rol'       => 'MAGISTRADO_PONENTE',
                    'region_id' => $regionMetro->id,
                    'sala_id'   => $sala1->id,
                    'area_id'   => $ponencia1S1->id,
                    'activo'    => false,
                ],
            ];

            // Inserción y sincronización
            foreach ($usuariosPrueba as $datos) {
                $rolClave = $datos['rol'];
                unset($datos['rol']);

                $usuario = User::firstOrCreate(
                    ['email' => $datos['email']],
                    array_merge($datos, ['password' => $passwordGenerica])
                );

                $usuario->roles()->syncWithoutDetaching([$roles[$rolClave]->id]);

                // Asignar salas al primer visitador para pruebas
                if ($datos['email'] === 'visitador.zona1@tfja.gob.mx') {
                    $sala1->update(['magistrado_visitador_id' => $usuario->id]);
                    $sala2->update(['magistrado_visitador_id' => $usuario->id]);
                }
            }
        });
    }
}