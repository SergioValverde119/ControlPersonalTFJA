<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('usuario'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Regla para Magistrados: Únicamente pueden cambiar la contraseña
        if (! $this->user()->hasRole('ADMIN_DGTIC')) {
            return [
                'password' => ['required', 'string', 'min:8'],
            ];
        }

        // Reglas para ADMIN_DGTIC: Puede editar el expediente completo
        $usuario = $this->route('usuario');
        $userId  = is_object($usuario) ? $usuario->id : $usuario;

        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'  => ['nullable', 'string', 'min:8'],
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => ['exists:roles,id'],
            'region_id' => ['nullable', 'exists:regiones,id'],
            'sala_id'   => [
                'nullable',
                Rule::exists('salas', 'id')->where(function ($query) {
                    if ($this->filled('region_id')) {
                        $query->where('region_id', $this->region_id);
                    }
                }),
            ],
            'area_id'   => [
                'nullable',
                Rule::exists('areas', 'id')->where(function ($query) {
                    if ($this->filled('sala_id')) {
                        $query->where('sala_id', $this->sala_id);
                    }
                }),
            ],
            'activo'    => ['boolean'],
        ];
    }
}