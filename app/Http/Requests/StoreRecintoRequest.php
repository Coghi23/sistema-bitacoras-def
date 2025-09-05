<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecintoRequest extends FormRequest
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
            'nombre' => 'required|string|max:55',
            'institucion_id' => 'required|array|min:1',
            'institucion_id.*' => 'required|exists:institucione,id',
            'llave_id' => 'required|exists:llave,id|unique:recinto,llave_id',
            'estadoRecinto_id' => 'required|exists:estadorecinto,id',
            'tipoRecinto_id' => 'required|exists:tiporecinto,id',
        ];
    }
   
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del  recinto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder los 55 caracteres.',
            'nombre.unique' => 'Ya existe un recinto con este nombre.',
            'id_institucion.required' => 'La institución es obligatoria.',
            'id_institucion.integer' => 'La institución debe ser un número entero.',
            'id_institucion.exists' => 'La institución seleccionada no es válida.',
            'id_llave.required' => 'La llave es obligatoria.',
            'id_llave.integer' => 'La llave debe ser un número entero.',
            'id_llave.exists' => 'La llave seleccionada no es válida.',
            'id_llave.unique' => 'Ya existe un recinto con esta llave.',
            
            'id_estadoRecinto.required' => 'El estado del recinto es obligatorio.',
            'id_estadoRecinto.integer' => 'El estado del recinto debe ser un número entero.',
            'id_estadoRecinto.exists' => 'El estado del recinto seleccionado no es válido.',
            'id_tipoRecinto.required' => 'El tipo de recinto es obligatorio.',
            'id_tipoRecinto.integer' => 'El tipo de recinto debe ser un número entero.',
            'id_tipoRecinto.exists' => 'El tipo de recinto seleccionado no es válido.',
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