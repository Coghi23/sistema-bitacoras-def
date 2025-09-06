<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Especialidade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSeccionRequest extends FormRequest
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
        $seccion = $this->route('seccion');
        $seccionId = $seccion->id;
        return [
            'nombre' => 'required|string|max:55|unique:seccione,nombre,' . $seccionId . ',id,condicion,1',
            'id_institucion' => 'nullable|exists:institucione,id',
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
            'id_institucion.exists' => 'La institución seleccionada no es válida.',
            'especialidades.required' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.array' => 'Las especialidades deben ser un listado válido.',
            'especialidades.min' => 'Debe seleccionar al menos una especialidad.',
            'especialidades.*.required' => 'Cada especialidad debe ser válida.',
            'especialidades.*.exists' => 'Una de las especialidades seleccionadas no existe.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $especialidades = (array) $this->input('especialidades', []);
            $institucionId = $this->input('id_institucion');

            if (empty($especialidades)) {
                return;
            }

            if ($institucionId) {
                if (Schema::hasTable('especialidad_institucion')) {
                    // Validar vía pivote: todas deben estar vinculadas a la institución enviada
                    $vinculadas = DB::table('especialidad_institucion')
                        ->whereIn('especialidad_id', $especialidades)
                        ->where('institucion_id', $institucionId)
                        ->distinct()
                        ->count('especialidad_id');

                    if ($vinculadas < count($especialidades)) {
                        $v->errors()->add('especialidades', 'Las especialidades seleccionadas deben pertenecer a la institución elegida.');
                    }
                } else {
                    // Fallback 1:N
                    $countMismatched = Especialidade::whereIn('id', $especialidades)
                        ->where('id_institucion', '!=', $institucionId)
                        ->count();

                    if ($countMismatched > 0) {
                        $v->errors()->add('especialidades', 'Las especialidades seleccionadas deben pertenecer a la institución elegida.');
                    }
                }
            } else {
                // Cuando no se especifica institución, exigir coherencia entre especialidades
                if (Schema::hasTable('especialidad_institucion')) {
                    // Debe existir al menos una institución común a todas las especialidades seleccionadas
                    $existeComun = DB::table('especialidad_institucion')
                        ->whereIn('especialidad_id', $especialidades)
                        ->groupBy('institucion_id')
                        ->havingRaw('COUNT(DISTINCT especialidad_id) = ?', [count($especialidades)])
                        ->exists();

                    if (!$existeComun) {
                        $v->errors()->add('especialidades', 'No puede seleccionar especialidades de diferentes instituciones sin una institución en común.');
                    }
                } else {
                    // Fallback 1:N: todas deben tener el mismo id_institucion
                    $distinctInstituciones = Especialidade::whereIn('id', $especialidades)
                        ->select('id_institucion')
                        ->distinct()
                        ->count();

                    if ($distinctInstituciones > 1) {
                        $v->errors()->add('especialidades', 'No puede seleccionar especialidades de diferentes instituciones.');
                    }
                }
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $seccion = $this->route('seccion');
        $response = redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_editar_id', $seccion->id);
        
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

}
