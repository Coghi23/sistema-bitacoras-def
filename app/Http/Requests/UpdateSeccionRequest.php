<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeccionRequest extends FormRequest
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
        $seccion = $this->route('seccion');
        $seccionId = $seccion->id;
        return [
            'nombre' => 'required|string|max:55',
            'especialidades' => 'required|array|min:1',
            'especialidades.*' => 'required|exists:especialidad,id',
        ];  
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la sección es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder los 55 caracteres.',
            'especialidades.required' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.array' => 'Las especialidades deben ser un listado válido.',
            'especialidades.min' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.*.required' => 'Cada especialidad debe ser válida.',
            'especialidades.*.exists' => 'Una de las especialidades seleccionadas no existe.',
        ];
    }
}
