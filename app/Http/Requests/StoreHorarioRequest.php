<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
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
            'idRecinto' => 'required|exists:recinto,id',
            'idSubarea' => 'required|exists:subarea,id',
            'idSeccion' => 'required|exists:seccione,id',
            'user_id' => 'required|exists:users,id',
            'tipoHorario' => 'required|in:fijo,temporal',
            'fecha' => 'nullable|date|required_if:tipoHorario,temporal',
            'dia' => 'nullable|string|required_if:tipoHorario,fijo|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'lecciones' => 'required|array|min:1',
            'lecciones.*' => 'exists:leccion,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required_if' => 'La fecha es obligatoria para horarios temporales.',
            'dia.required_if' => 'El día es obligatorio para horarios fijos.',
            'lecciones.required' => 'Debe seleccionar al menos una lección.',
            'lecciones.min' => 'Debe seleccionar al menos una lección.',
        ];
    }
}
