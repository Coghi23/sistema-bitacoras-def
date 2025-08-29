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
        $rules = [
            'id_recinto' => 'required|exists:recinto,id',
            'id_seccion' => 'required|exists:seccione,id',
            'id_subarea' => 'required|exists:subarea,id',
            'id_horario' => 'required|exists:horarios,id',
            'user_id' => 'required|exists:users,id',
            'hora_envio' => 'required|date_format:H:i',
        ];

        // Si es un reporte de problema, agregar validaciones adicionales
        if ($this->input('estado') === 'problema') {
            $rules['prioridad'] = 'required|in:alta,media,regular,baja';
            $rules['observaciones'] = 'required|string|min:10|max:500';
        }

        return $rules;
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
            'user_id.required' => 'El profesor es obligatorio.',
            'user_id.exists' => 'El profesor seleccionado no existe.',
            'prioridad.required' => 'La prioridad es obligatoria para reportar un problema.',
            'prioridad.in' => 'La prioridad debe ser alta, media, regular o baja.',
            'observaciones.required' => 'Las observaciones son obligatorias para reportar un problema.',
            'observaciones.min' => 'Las observaciones deben tener al menos 10 caracteres.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }
}


