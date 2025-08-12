<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
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
            'idBitacora' => 'required|exists:bitacora,id',
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'descripcion' => 'required|string|max:255',
        ];
    }
}
