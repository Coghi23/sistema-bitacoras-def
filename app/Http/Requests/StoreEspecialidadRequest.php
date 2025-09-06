<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEspecialidadRequest extends FormRequest
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
            'nombre' => 'required|string|max:50|unique:especialidad,nombre',
            'instituciones' => 'required|array|min:1',
            'instituciones.*' => 'integer|exists:institucione,id'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre.unique' => 'Ya existe una especialidad con este nombre.',
            'nombre.max' => 'El nombre no puede exceder los 50 caracteres.',
            'instituciones.required' => 'Debe seleccionar al menos una institución.',
            'instituciones.array' => 'El formato de instituciones no es válido.',
            'instituciones.min' => 'Debe seleccionar al menos una institución.',
            'instituciones.*.exists' => 'Alguna institución seleccionada no existe.'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_crear', true);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
