<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLlaveRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:llave,nombre',
        ];
    }
    /**
     * Mensajes personalizados de validación
     */
    public function messages()
    {
        return [
            'nombre.unique' => 'Ya existe una llave con ese nombre.',
            'nombre.required' => 'El nombre de la llave es obligatorio.',
        ];
    }
}
