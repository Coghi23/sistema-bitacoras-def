<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHorarioRequest extends FormRequest
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
            'idRecinto' => 'required|exists:recintos,id',
            'idSubareaSeccion' => 'nullable|exists:subarea_seccions,id',
            'user_id' => 'required|exists:profesors,id',
            'tipoHorario' => 'required|string|max:50',
            'horaEntrada' => 'required|date_format:H:i',
            'horaSalida' => 'required|date_format:H:i|after:horaEntrada',
            'dia' => 'required|string|max:10',
            'condicion' => 'required|string|max:20'
        ];
    }
}
