<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeccionRequest extends FormRequest
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
        $leccion = $this->route('leccion');
        $leccionId = $leccion->id;
        return [
            'leccion' => 'required|string|max:55|unique:leccion,leccion,' . $leccionId,
            'idTipoLeccion' => 'required|integer|exists:tipo_leccion,id',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
        ];
    }
}
