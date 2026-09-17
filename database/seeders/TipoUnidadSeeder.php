<?php

// database/seeders/TipoUnidadSeeder.php
namespace Database\Seeders;

use App\Models\TipoUnidad;
use Illuminate\Database\Seeder;

class TipoUnidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['clave' => 'RAIZ', 'nombre' => 'Órgano Central / TFJA', 'escala_inmediato' => false],
            ['clave' => 'TERRITORIO', 'nombre' => 'Región Territorial / Visitaduría', 'escala_inmediato' => false],
            ['clave' => 'SEDE', 'nombre' => 'Sede / Sala Regional Ordinaria o Especializada', 'escala_inmediato' => false],
            ['clave' => 'PONENCIA', 'nombre' => 'Ponencia Jurisdiccional', 'escala_inmediato' => false],
            ['clave' => 'AREA_ADMINISTRATIVA', 'nombre' => 'Dirección Administrativa Central (SOA)', 'escala_inmediato' => false],
            ['clave' => 'SOPORTE_COMPARTIDO', 'nombre' => 'Área de Soporte Común (Archivo, Oficialía)', 'escala_inmediato' => true],
        ];

        foreach ($tipos as $tipo) {
            TipoUnidad::updateOrCreate(['clave' => $tipo['clave']], $tipo);
        }
    }
}