<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcesarFirmaRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\TramiteFirma $firma */
        $firma = $this->route('firma');

        // Solo el usuario asignado a la firma puede emitir la decisión
        return $firma && $this->user()?->id === $firma->firmante_user_id;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', Rule::in(['AUTORIZADO', 'RECHAZADO'])],
            'motivo'   => [
                Rule::requiredIf(fn () => $this->input('decision') === 'RECHAZADO'),
                'nullable',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Debe indicar si autoriza o rechaza el turno de firma.',
            'decision.in'       => 'La decisión seleccionada no es válida.',
            'motivo.required'   => 'Es obligatorio asentar el motivo o fundamento del rechazo.',
            'motivo.min'        => 'El motivo del rechazo debe ser de al menos 10 caracteres.',
        ];
    }
}
