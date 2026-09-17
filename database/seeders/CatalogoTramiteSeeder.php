<?php

namespace Database\Seeders;

use App\Models\CatalogoEstatusTramite;
use App\Models\CatalogoTipoDocumento;
use Illuminate\Database\Seeder;

class CatalogoTramiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Catálogo de Estatus del Flujo (Carriles 1 al 7)
        $estatus = [
            [
                'clave' => 'BORRADOR',
                'nombre' => 'Borrador / Captura Inicial',
                'badge_color' => 'gray',
                'orden_flujo' => 1,
                'permite_edicion' => true,
                'es_terminal' => false,
            ],
            [
                'clave' => 'VALIDACION_ANTECEDENTES',
                'nombre' => 'En Validación de Antecedentes / Venia',
                'badge_color' => 'amber',
                'orden_flujo' => 2,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'AUTORIZACION_JERARQUICA',
                'nombre' => 'En Revisión Jerárquica / Presidencia',
                'badge_color' => 'blue',
                'orden_flujo' => 3,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'DICTAMEN_REGIONAL',
                'nombre' => 'En Dictamen Técnico / Visitaduría',
                'badge_color' => 'indigo',
                'orden_flujo' => 4,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'MESA_TECNICA_RH',
                'nombre' => 'En Mesa Técnica de Recursos Humanos',
                'badge_color' => 'purple',
                'orden_flujo' => 5,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'OBSERVADO',
                'nombre' => 'Devuelto con Observaciones',
                'badge_color' => 'rose',
                'orden_flujo' => 5,
                'permite_edicion' => true,
                'es_terminal' => false,
            ],
            [
                'clave' => 'VPDO_CORTE',
                'nombre' => 'Validado para Corte Semanal',
                'badge_color' => 'teal',
                'orden_flujo' => 6,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'EN_SESION_JGA',
                'nombre' => 'En Sesión de Junta de Gobierno',
                'badge_color' => 'amber',
                'orden_flujo' => 7,
                'permite_edicion' => false,
                'es_terminal' => false,
            ],
            [
                'clave' => 'AUTORIZADO',
                'nombre' => 'Autorizado Definitivo',
                'badge_color' => 'emerald',
                'orden_flujo' => 7,
                'permite_edicion' => false,
                'es_terminal' => true,
            ],
            [
                'clave' => 'DESECHADO',
                'nombre' => 'Desechado / No Aprobado',
                'badge_color' => 'red',
                'orden_flujo' => 7,
                'permite_edicion' => false,
                'es_terminal' => true,
            ],
        ];

        foreach ($estatus as $item) {
            CatalogoEstatusTramite::updateOrCreate(['clave' => $item['clave']], $item);
        }

        // 2. Catálogo Oficial de Documentos (Perennes y Transaccionales)
        $documentos = [
            // Perennes (Expediente de Vida de la Persona)
            ['clave' => 'DOC_ACTA_NAC', 'nombre' => 'Acta de Nacimiento', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_INE', 'nombre' => 'Credencial para Votar (INE)', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => true],
            ['clave' => 'DOC_CURP', 'nombre' => 'CURP Certificada', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_RFC_SAT', 'nombre' => 'Constancia de Situación Fiscal', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => true],
            ['clave' => 'DOC_TITULO', 'nombre' => 'Título Profesional', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_CEDULA', 'nombre' => 'Cédula Profesional', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_COMP_DOMICILIO', 'nombre' => 'Comprobante de Domicilio', 'alcance' => 'PERSONA', 'es_obligatorio' => true, 'requiere_vigencia' => true],
            ['clave' => 'DOC_CONSTANCIA_LABORAL', 'nombre' => 'Constancia de Experiencia Laboral', 'alcance' => 'PERSONA', 'es_obligatorio' => false, 'requiere_vigencia' => false],

            // Transaccionales (Exclusivos del Folio en Trámite)
            ['clave' => 'DOC_OFICIO_PROPUESTA', 'nombre' => 'Oficio de Propuesta Formal', 'alcance' => 'TRAMITE', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_CV', 'nombre' => 'Currículum Vitae Actualizado', 'alcance' => 'TRAMITE', 'es_obligatorio' => true, 'requiere_vigencia' => false],
            ['clave' => 'DOC_NO_INHABILITADO', 'nombre' => 'Constancia de No Inhabilitación (SFP)', 'alcance' => 'TRAMITE', 'es_obligatorio' => true, 'requiere_vigencia' => true],
            ['clave' => 'DOC_CARTA_RENUNCIA', 'nombre' => 'Carta de Renuncia Voluntaria', 'alcance' => 'TRAMITE', 'es_obligatorio' => false, 'requiere_vigencia' => false],
            ['clave' => 'DOC_ENTREGA_RECEPCION', 'nombre' => 'Acta de Entrega-Recepción', 'alcance' => 'TRAMITE', 'es_obligatorio' => false, 'requiere_vigencia' => false],
        ];

        foreach ($documentos as $doc) {
            CatalogoTipoDocumento::updateOrCreate(['clave' => $doc['clave']], $doc);
        }
    }
}
