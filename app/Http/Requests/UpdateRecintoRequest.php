<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubareaRequest extends FormRequest
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
        $recinto = $this->route('recinto');
        
        $recintoId = $recinto->id;
        return [
            'nombre' => 'required|string|max:55',
            'estado' => 'required|string|max:55',
            'tipo' => 'required|string|max:55'


        ]; 
    }
}
