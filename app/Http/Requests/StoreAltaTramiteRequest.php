<?php

namespace App\Http\Requests;

use App\Models\Plaza;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAltaTramiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->hasRole('ADMIN_DGTIC') || $user->titularidadActiva()->exists());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'curp'             => strtoupper(trim((string) $this->input('curp'))),
            'rfc'              => strtoupper(trim((string) $this->input('rfc'))),
            'nombre'           => trim((string) $this->input('nombre')),
            'primer_apellido'  => trim((string) $this->input('primer_apellido')),
            'segundo_apellido' => trim((string) $this->input('segundo_apellido')),
        ]);
    }

    public function rules(): array
    {
        $user = $this->user();
        $unidadPropiaId = $user->titularidadActiva?->unidad_organizacional_id;

        return [
            // 1. PLAZA DESTINO: Vacante y de la oficina base del solicitante
            'plaza_destino_id' => [
                'required',
                'integer',
                Rule::exists('plazas', 'id')->where(function ($query) use ($user, $unidadPropiaId) {
                    $query->where('estatus', 'VACANTE');

                    if (! $user->hasRole('ADMIN_DGTIC')) {
                        $query->where('unidad_organizacional_id', $unidadPropiaId);
                    }
                }),
            ],

            // 2. VIGENCIA PROPUESTA
            'fecha_efectos_propuesta' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            // 3. IDENTIDAD BÁSICA DEL ASPIRANTE (PERSONAS)
            'curp' => [
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z\d]\d$/',
            ],
            'rfc' => [
                'required',
                'string',
                'min:10',
                'max:13',
                'regex:/^[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z\d]{2}$/',
            ],
            'nombre'           => ['required', 'string', 'max:100'],
            'primer_apellido'  => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['nullable', 'string', 'max:100'],
            'correo_contacto'  => ['nullable', 'email', 'max:255'],
            'telefono_contacto'=> ['nullable', 'string', 'max:20'],

            // 4. CARGA DOCUMENTAL LIGERA (Exclusiva de Fase 1)
            'oficio_propuesta' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'curriculum_vitae' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'plaza_destino_id.required' => 'Debe seleccionar la plaza vacante a proponer.',
            'plaza_destino_id.exists'   => 'La plaza seleccionada no está vacante o no pertenece a su oficina.',
            'fecha_efectos_propuesta.required' => 'Indique la fecha de efectos de la propuesta.',
            'fecha_efectos_propuesta.after_or_equal' => 'La fecha de efectos debe ser igual o posterior a hoy.',
            'curp.required'             => 'La CURP del aspirante es obligatoria.',
            'curp.regex'                => 'El formato de la CURP es inválido.',
            'rfc.required'              => 'El RFC del aspirante es obligatorio.',
            'rfc.regex'                 => 'El formato del RFC es inválido.',
            'oficio_propuesta.required' => 'Debe adjuntar el Oficio de Propuesta firmado en formato PDF.',
            'curriculum_vitae.required' => 'Debe adjuntar el Currículum Vitae (CV) del candidato en formato PDF.',
            '*.mimes'                   => 'Los archivos deben estar en formato PDF.',
            '*.max'                     => 'Los archivos PDF no deben exceder 10 MB.',
        ];
    }
}
