<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Region;
use App\Models\Sala;
use Illuminate\Database\Seeder;

class EstructuraSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo = [
            ['mv' => 'ACC', 'reg_nom' => 'Región Baja California', 'reg_c' => 'REG_BC', 'sede' => 'Tijuana / Mexicali', 'sala' => 'Primera Sala Regional en Baja California', 'c' => 'SR_BC_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Baja California', 'reg_c' => 'REG_BC', 'sede' => 'Tijuana / Mexicali', 'sala' => 'Segunda Sala Regional en Baja California', 'c' => 'SR_BC_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Sonora', 'reg_c' => 'REG_SON', 'sede' => 'Cd. Obregón', 'sala' => 'Sala Regional en Sonora', 'c' => 'SR_SON_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Sinaloa', 'reg_c' => 'REG_SIN', 'sede' => 'Cd. de Culiacán', 'sala' => 'Primera Sala Regional en Sinaloa', 'c' => 'SR_SIN_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Sinaloa', 'reg_c' => 'REG_SIN', 'sede' => 'Cd. de Culiacán', 'sala' => 'Segunda Sala Regional en Sinaloa', 'c' => 'SR_SIN_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Chihuahua', 'reg_c' => 'REG_CHIH', 'sede' => 'Cd. de Chihuahua', 'sala' => 'Sala Regional en Chihuahua', 'c' => 'SR_CHIH_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Coahuila', 'reg_c' => 'REG_COAH', 'sede' => 'Cd. de Torreón', 'sala' => 'Primera Sala Regional en Coahuila', 'c' => 'SR_COAH_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Coahuila', 'reg_c' => 'REG_COAH', 'sede' => 'Cd. de Torreón', 'sala' => 'Segunda Sala Regional en Coahuila', 'c' => 'SR_COAH_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Coahuila', 'reg_c' => 'REG_COAH', 'sede' => 'Cd. de Torreón', 'sala' => 'Tercera Sala Regional en Coahuila y Auxiliar', 'c' => 'SR_COAH_03_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Zacatecas', 'reg_c' => 'REG_ZAC', 'sede' => 'Cd. de Zacatecas', 'sala' => 'Sala Regional en Zacatecas y Auxiliar', 'c' => 'SR_ZAC_01_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Nuevo León', 'reg_c' => 'REG_NL', 'sede' => 'San Pedro Garza García', 'sala' => 'Primera Sala Regional en Nuevo León', 'c' => 'SR_NL_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Nuevo León', 'reg_c' => 'REG_NL', 'sede' => 'San Pedro Garza García', 'sala' => 'Segunda Sala Regional en Nuevo León', 'c' => 'SR_NL_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Nuevo León', 'reg_c' => 'REG_NL', 'sede' => 'San Pedro Garza García', 'sala' => 'Tercera Sala Regional en Nuevo León', 'c' => 'SR_NL_03', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Nuevo León', 'reg_c' => 'REG_NL', 'sede' => 'San Pedro Garza García', 'sala' => 'Cuarta Sala Regional en Nuevo León', 'c' => 'SR_NL_04', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Jalisco', 'reg_c' => 'REG_JAL', 'sede' => 'Cd. de Guadalajara', 'sala' => 'Primera Sala Regional en Jalisco', 'c' => 'SR_JAL_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Jalisco', 'reg_c' => 'REG_JAL', 'sede' => 'Cd. de Guadalajara', 'sala' => 'Segunda Sala Regional en Jalisco', 'c' => 'SR_JAL_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Jalisco', 'reg_c' => 'REG_JAL', 'sede' => 'Cd. de Guadalajara', 'sala' => 'Tercera Sala Regional en Jalisco', 'c' => 'SR_JAL_03', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Aguascalientes', 'reg_c' => 'REG_AGS', 'sede' => 'Cd. de Aguascalientes', 'sala' => 'Sala Regional en Aguascalientes', 'c' => 'SR_AGS_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Querétaro', 'reg_c' => 'REG_QRO', 'sede' => 'Cd. de Querétaro', 'sala' => 'Sala Regional en Querétaro', 'c' => 'SR_QRO_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Guanajuato I', 'reg_c' => 'REG_GTO1', 'sede' => 'Cd. de Celaya', 'sala' => 'Sala Regional en Guanajuato I', 'c' => 'SR_GTO1_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Guanajuato II', 'reg_c' => 'REG_GTO2', 'sede' => 'Mpio. de Silao', 'sala' => 'Sala Regional en Guanajuato II', 'c' => 'SR_GTO2_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Estado de México I', 'reg_c' => 'REG_EM1', 'sede' => 'Mpio. de Naucalpan', 'sala' => 'Primera Sala Regional en el Estado de México I', 'c' => 'SR_EM1_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Estado de México I', 'reg_c' => 'REG_EM1', 'sede' => 'Mpio. de Naucalpan', 'sala' => 'Segunda Sala Regional en el Estado de México I', 'c' => 'SR_EM1_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Estado de México II', 'reg_c' => 'REG_EM2', 'sede' => 'Cd. de Toluca', 'sala' => 'Sala Regional en el Estado de México II y Auxiliar', 'c' => 'SR_EM2_01_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Puebla', 'reg_c' => 'REG_PUE', 'sede' => 'Mpio. de San Andrés Cholula', 'sala' => 'Primera Sala Regional en Puebla', 'c' => 'SR_PUE_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Puebla', 'reg_c' => 'REG_PUE', 'sede' => 'Mpio. de San Andrés Cholula', 'sala' => 'Segunda Sala Regional en Puebla', 'c' => 'SR_PUE_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Puebla', 'reg_c' => 'REG_PUE', 'sede' => 'Mpio. de San Andrés Cholula', 'sala' => 'Tercera Sala Regional en Puebla y Auxiliar', 'c' => 'SR_PUE_03_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Veracruz', 'reg_c' => 'REG_VER', 'sede' => 'Cd. de Xalapa-Enríquez', 'sala' => 'Primera Sala Regional en Veracruz', 'c' => 'SR_VER_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Veracruz', 'reg_c' => 'REG_VER', 'sede' => 'Cd. de Xalapa-Enríquez', 'sala' => 'Segunda Sala Regional en Veracruz', 'c' => 'SR_VER_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Guerrero', 'reg_c' => 'REG_GRO', 'sede' => 'Cd. de Acapulco', 'sala' => 'Sala Regional en Guerrero', 'c' => 'SR_GRO_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Oaxaca', 'reg_c' => 'REG_OAX', 'sede' => 'Cd. de Oaxaca', 'sala' => 'Sala Regional en Oaxaca', 'c' => 'SR_OAX_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Yucatán', 'reg_c' => 'REG_YUC', 'sede' => 'Cd. de Mérida', 'sala' => 'Sala Regional en Yucatán', 'c' => 'SR_YUC_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Primera Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Segunda Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_02', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Tercera Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_03', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Cuarta Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_04', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Quinta Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_05', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Sexta Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_06', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Séptima Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_07', 'tipo' => 'ORDINARIA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Octava Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_08', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Novena Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_09', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Décima Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_10', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Décimo Primera Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_11', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Décimo Segunda Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_12', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Primera Sala Auxiliar en Materia de Responsabilidades Administrativas Graves', 'c' => 'SA_CDMX_RAG_01', 'tipo' => 'AUXILIAR'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Segunda Sala Auxiliar en Materia de Responsabilidades Administrativas Graves', 'c' => 'SA_CDMX_RAG_02', 'tipo' => 'AUXILIAR'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Décimo Cuarta Sala Regional en la Ciudad de México', 'c' => 'SR_CDMX_14', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Primera Sala Especializada en Materia Ambiental y de Regulación', 'c' => 'SE_CDMX_AMB_01', 'tipo' => 'ESPECIALIZADA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Sala Especializada en Materia de Propiedad Intelectual', 'c' => 'SE_CDMX_PI', 'tipo' => 'ESPECIALIZADA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Segunda Sala Especializada en Materia Ambiental y de Regulación', 'c' => 'SE_CDMX_AMB_02', 'tipo' => 'ESPECIALIZADA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Ciudad de México', 'reg_c' => 'REG_CDMX', 'sede' => 'Ciudad de México', 'sala' => 'Sala Especializada en Materia del Juicio de Resolución Exclusiva de Fondo', 'c' => 'SE_CDMX_JRF', 'tipo' => 'ESPECIALIZADA'],
            ['mv' => 'CHRF', 'reg_nom' => 'Región Tamaulipas', 'reg_c' => 'REG_TAM', 'sede' => 'Cd. Victoria', 'sala' => 'Sala Regional en Tamaulipas', 'c' => 'SR_TAM_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Chiapas', 'reg_c' => 'REG_CHIS', 'sede' => 'Cd. de Tuxtla Gutiérrez', 'sala' => 'Sala Regional en Chiapas', 'c' => 'SR_CHIS_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Quintana Roo', 'reg_c' => 'REG_QROO', 'sede' => 'Cancún (Mpio. de Benito Juárez)', 'sala' => 'Sala Regional en Quintana Roo y Auxiliar', 'c' => 'SR_QROO_01_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'LVAA', 'reg_nom' => 'Región Michoacán', 'reg_c' => 'REG_MICH', 'sede' => 'Cd. de Morelia', 'sala' => 'Sala Regional en Michoacán', 'c' => 'SR_MICH_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'ACC', 'reg_nom' => 'Región Morelos', 'reg_c' => 'REG_MOR', 'sede' => 'Cd. de Cuernavaca', 'sala' => 'Sala Regional en Morelos y Auxiliar', 'c' => 'SR_MOR_01_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'MCH', 'reg_nom' => 'Región San Luis Potosí', 'reg_c' => 'REG_SLP', 'sede' => 'Cd. de San Luis Potosí', 'sala' => 'Sala Regional en San Luis Potosí', 'c' => 'SR_SLP_01', 'tipo' => 'ORDINARIA'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Tabasco', 'reg_c' => 'REG_TAB', 'sede' => 'Cd. de Villahermosa', 'sala' => 'Sala Regional en Tabasco y Auxiliar', 'c' => 'SR_TAB_01_AUX', 'tipo' => 'AUXILIAR'],
            ['mv' => 'MCH', 'reg_nom' => 'Región Hidalgo', 'reg_c' => 'REG_HGO', 'sede' => 'Cd. de Pachuca', 'sala' => 'Sala Regional en Hidalgo', 'c' => 'SR_HGO_01', 'tipo' => 'ORDINARIA'],
        ];

        foreach ($catalogo as $item) {
            // 1. Crear o recuperar la Región
            $region = Region::firstOrCreate(
                ['clave' => $item['reg_c']],
                [
                    'nombre' => $item['reg_nom'],
                    'sede' => $item['sede'],
                    'activo' => true,
                ]
            );

            // 2. Crear o actualizar la Sala con su Magistrado Visitador
            $sala = Sala::updateOrCreate(
                ['clave' => $item['c']],
                [
                    'region_id' => $region->id,
                    'nombre' => $item['sala'],
                    'tipo' => $item['tipo'],
                    'magistrado_visitador' => $item['mv'],
                    'activo' => true,
                ]
            );

            // 3. Crear las 3 Ponencias de la Sala
            for ($num = 1; $num <= 3; $num++) {
                Area::updateOrCreate(
                    ['clave' => "{$sala->clave}_P{$num}"],
                    [
                        'sala_id' => $sala->id,
                        'nombre' => "Ponencia {$num}",
                        'tipo' => 'PONENCIA',
                        'numero' => $num,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
