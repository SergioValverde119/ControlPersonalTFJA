<?php


namespace App\Http\Requests;

use App\Actions\Workflow\ProcesarFirmaAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcesarFirmaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Se valida que la petición provenga de un usuario autenticado.
        // La validación de si es exactamente el firmante en turno la resuelve la Action.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                'string',
                Rule::in([
                    ProcesarFirmaAction::DECISION_APROBAR,
                    ProcesarFirmaAction::DECISION_DEVOLVER,
                ]),
            ],
            'motivo' => [
                'required_if:decision,' . ProcesarFirmaAction::DECISION_DEVOLVER,
                'nullable',
                'string',
                'min:5',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'La decisión de la firma es obligatoria.',
            'decision.in' => 'La decisión debe ser APROBADO o DEVUELTO_OBSERVADO.',
            'motivo.required_if' => 'Es obligatorio detallar el motivo de la devolución u observación.',
            'motivo.min' => 'El motivo debe contener al menos 5 caracteres.',
            'motivo.max' => 'El motivo no puede exceder los 1000 caracteres.',
        ];
    }
}
