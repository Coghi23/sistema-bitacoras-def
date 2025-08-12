<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBitacoraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_recinto' => 'required|exists:recinto,id',
            'id_seccion' => 'required|exists:seccione,id',
            'id_subarea' => 'required|exists:subarea,id',
            'id_horario' => 'required|exists:horario,id',
            'id_usuario' => 'required|exists:users,id',
            'hora_envio' => 'required|date_format:H:i',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id_recinto.required' => 'El recinto es obligatorio.',
            'id_recinto.exists' => 'El recinto seleccionado no existe.',
            'id_seccion.required' => 'La sección es obligatoria.',
            'id_seccion.exists' => 'La sección seleccionada no existe.',
            'id_subarea.required' => 'La subárea es obligatoria.',
            'id_subarea.exists' => 'La subárea seleccionada no existe.',
            'id_horario.required' => 'El horario es obligatorio.',
            'id_horario.exists' => 'El horario seleccionado no existe.',
            'id_usuario.required' => 'El profesor es obligatorio.',
            'id_usuario.exists' => 'El profesor seleccionado no existe.',
        ];
    }
}
