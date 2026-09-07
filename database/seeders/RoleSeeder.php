<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'clave' => 'ADMIN_DGTIC',
                'nombre' => 'Administrador DGTIC',
                'descripcion' => 'Administración técnica del sistema, catálogos y soporte de cuentas.',
            ],
            [
                'clave' => 'MAGISTRADO_PRESIDENTE',
                'nombre' => 'Magistrado Presidente de Sala',
                'descripcion' => 'Titular de la Presidencia de Sala. Vistos buenos de áreas comunes y trámites de Sala.',
            ],
            [
                'clave' => 'MAGISTRADO_PONENTE',
                'nombre' => 'Magistrado Ponente',
                'descripcion' => 'Titular de Ponencia. Propone y autoriza movimientos y trámites de su equipo.',
            ],
            [
                'clave' => 'MAGISTRADO_VISITADOR',
                'nombre' => 'Magistrado Visitador',
                'descripcion' => 'Supervisión e inspección administrativa sobre la Región asignada.',
            ],
            [
                'clave' => 'RECURSOS_HUMANOS',
                'nombre' => 'Recursos Humanos / Nóminas',
                'descripcion' => 'Validación normativa central, aplicación de bajas, licencias e incidencias.',
            ],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(
                ['clave' => $rol['clave']], // Condición para no duplicar
                [
                    'nombre' => $rol['nombre'],
                    'descripcion' => $rol['descripcion'],
                    'activo' => true,
                ]
            );
        }
    }
}
