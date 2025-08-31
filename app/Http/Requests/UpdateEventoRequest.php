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
        $evento = $this->route('evento');
        $eventoId = $evento->id;
        return [
            'id_Bitacora' => 'required|exists:bitacora,id',
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'observacion' => 'required|string|max:255',
            'prioridad' => 'required|integer',
            'confirmacion' => 'required|boolean',
            'descripcion' => 'required|string|max:255',
            'condicion' => 'required|boolean',
        ];
    }
}
