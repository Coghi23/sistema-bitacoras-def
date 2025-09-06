<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Especialidade;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class StoreSeccionRequest extends FormRequest
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
            // Unicidad solo entre secciones activas (condicion = 1)
            'nombre' => 'required|string|max:55|unique:seccione,nombre,NULL,id,condicion,1',
            'id_institucion' => 'required|exists:institucione,id',
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
            'nombre.unique' => 'Ya existe una sección con este nombre.',
            'id_institucion.required' => 'Debe seleccionar una institución.',
            'id_institucion.exists' => 'La institución seleccionada no es válida.',
            'especialidades.required' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.array' => 'Las especialidades deben ser un listado válido.',
            'especialidades.min' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.*.required' => 'Cada especialidad debe ser válida.',
            'especialidades.*.exists' => 'Una de las especialidades seleccionadas no existe.',
        ];
    }

    /**
     * Reglas adicionales: todas las especialidades deben pertenecer a la institución seleccionada
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $especialidades = (array) $this->input('especialidades', []);
            $institucionId = $this->input('id_institucion');

            if (!$institucionId || empty($especialidades)) {
                return;
            }


            // Si existe una tabla pivote many-to-many, validar contra ella.
            if (Schema::hasTable('especialidad_institucion')) {
                // Contar cuántas especialidades seleccionadas están vinculadas a la institución dada.
                $vinculadas = DB::table('especialidad_institucion')
                    ->whereIn('especialidad_id', $especialidades)
                    ->where('institucion_id', $institucionId)
                    ->distinct()
                    ->count('especialidad_id');

                if ($vinculadas < count($especialidades)) {
                    $v->errors()->add('especialidades', 'Las especialidades seleccionadas deben pertenecer a la institución elegida.');
                }
            } else {
                // Fallback al esquema actual 1:N (columna id_institucion en especialidad)
                $countMismatched = Especialidade::whereIn('id', $especialidades)
                    ->where('id_institucion', '!=', $institucionId)
                    ->count();

                if ($countMismatched > 0) {
                    $v->errors()->add('especialidades', 'Las especialidades seleccionadas deben pertenecer a la institución elegida.');
                }
            }
        });
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
