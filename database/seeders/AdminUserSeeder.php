<?php

// database/seeders/AdminUserSeeder.php
namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Identidad civil base institucional para el área técnica
        $personaAdmin = Persona::firstOrCreate(
            ['curp' => 'DGTIC000000HTFJA0'],
            [
                'rfc' => 'DGT000000TF1',
                'nombre' => 'Administrador',
                'primer_apellido' => 'DGTIC',
                'segundo_apellido' => 'Institucional',
                'telefono' => '5550037000',
            ]
        );

        // 2. Cuenta de usuario del Administrador
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin.dgtic@tfja.gob.mx'],
            [
                'name' => 'Administrador DGTIC',
                'password' => Hash::make('AdminTFJA2026!'),
                'persona_id' => $personaAdmin->id,
                'activo' => true,
            ]
        );

        // 3. Asignación del rol ADMIN_DGTIC existente
        $rolAdmin = Role::where('clave', 'ADMIN_DGTIC')->first();
        if ($rolAdmin) {
            $userAdmin->roles()->syncWithoutDetaching([$rolAdmin->id]);
        }
    }
}