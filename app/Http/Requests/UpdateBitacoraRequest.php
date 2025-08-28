<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBitacoraRequest extends FormRequest
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
            'id_recinto' => 'sometimes|required|exists:recinto,id',
            'id_seccion' => 'sometimes|required|exists:seccione,id',
            'id_subarea' => 'sometimes|required|exists:subarea,id',
            'id_horario' => 'sometimes|required|exists:horarios,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'hora_envio' => 'sometimes|required|date_format:H:i',
        ];
    }
}
