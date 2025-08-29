<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Horario;

class StoreHorarioRequest extends FormRequest
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
            'idRecinto' => 'required|integer|exists:recinto,id',
            'idSubarea' => 'required|integer|exists:subarea,id',
            'idSeccion' => 'required|integer|exists:seccione,id',
            'user_id' => 'required|integer|exists:users,id',
            'tipoHorario' => 'required|string|in:fijo,temporal',
            'fecha' => [
                'nullable',
                'date',
                'required_if:tipoHorario,temporal'
            ],
            'dia' => [
                'nullable',
                'string',
                'required_if:tipoHorario,fijo',
                'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'
            ],
            'lecciones' => 'required|array|min:1',
            'lecciones.*' => 'integer|exists:leccion,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que se seleccione al menos una lección
            if (!$this->has('lecciones') || empty($this->lecciones)) {
                $validator->errors()->add('lecciones', 'Debe seleccionar al menos una lección.');
                return;
            }

            // PRIORIDAD 1: Validar que la misma sección de la misma especialidad no tenga conflictos
            $this->validateSameSectionSpecialtyConflicts($validator);

            // PRIORIDAD 2: Validar conflictos entre horarios temporales y fijos
            $this->validateTemporalFixedConflicts($validator);

            // PRIORIDAD 3: Validar conflictos de horario: Un profesor no puede estar en múltiples recintos al mismo tiempo
            $this->validateTimeConflicts($validator);

            // PRIORIDAD 4: Validar conflictos en el mismo recinto: Dos profesores no pueden estar en el mismo recinto al mismo tiempo
            $this->validateSameRecintoConflicts($validator);

            // PRIORIDAD 5: Validar conflictos de secciones: Un profesor no puede tener dos secciones de la misma subárea al mismo tiempo
            $this->validateSameSubareaConflicts($validator);

            // PRIORIDAD 6: Validar que las lecciones seleccionadas sean consecutivas
            $this->validateConsecutiveLecciones($validator);

            // PRIORIDAD 7: Validar que no exista un horario duplicado para el mismo profesor, recinto y fecha/día (menos restrictivo)
            $query = Horario::where('user_id', $this->user_id)
                ->where('idRecinto', $this->idRecinto)
                ->where('condicion', 1);

            if ($this->tipoHorario === 'fijo') {
                $query->where('dia', $this->dia)
                      ->where('tipoHorario', 1);
            } else {
                // Para horarios temporales, verificar contra la fecha específica
                $query->where('fecha', $this->fecha)
                      ->where('tipoHorario', 0);
            }

            if ($query->exists()) {
                $tipo = $this->tipoHorario === 'fijo' ? 'día' : 'fecha';
                $valor = $this->tipoHorario === 'fijo' ? $this->dia : $this->fecha;
                $validator->errors()->add('general', "Ya existe un horario para este profesor en este recinto para el {$tipo}: {$valor}");
            }
        });
    }

    /**
     * Validar conflictos de tiempo para el mismo profesor
     */
    private function validateTimeConflicts($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones)) {
            return;
        }

        // Obtener las lecciones seleccionadas con sus horarios
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)->get();

        // Buscar horarios existentes del mismo profesor
        $horariosExistentes = collect();

        if ($this->tipoHorario === 'fijo') {
            // Para horarios fijos, buscar otros horarios fijos del mismo día
            $horariosExistentes = Horario::where('user_id', $this->user_id)
                ->where('dia', $this->dia)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with('leccion')
                ->get();
        } else {
            // Para horarios temporales, buscar horarios fijos del día correspondiente y otros temporales de la misma fecha
            $diaSemana = date('l', strtotime($this->fecha));
            $diasEspanol = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $diaEnEspanol = $diasEspanol[$diaSemana];

            // Horarios fijos del mismo día de la semana
            $horariosFijos = Horario::where('user_id', $this->user_id)
                ->where('dia', $diaEnEspanol)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with('leccion')
                ->get();

            // Otros horarios temporales de la misma fecha
            $horariosTemporales = Horario::where('user_id', $this->user_id)
                ->where('fecha', $this->fecha)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->with('leccion')
                ->get();

            $horariosExistentes = $horariosFijos->merge($horariosTemporales);
        }

        // Verificar conflictos de tiempo
        foreach ($horariosExistentes as $horarioExistente) {
            // Si es el mismo recinto, ya se validó arriba
            if ($horarioExistente->idRecinto == $this->idRecinto) {
                continue;
            }

            foreach ($horarioExistente->leccion as $leccionExistente) {
                foreach ($leccionesSeleccionadas as $leccionNueva) {
                    if ($this->hayConflictoHorario($leccionExistente, $leccionNueva)) {
                        $recintoExistente = $horarioExistente->recinto->nombre ?? 'Recinto desconocido';
                        $tipoExistente = $horarioExistente->tipoHorario == 1 ? 'fijo' : 'temporal';
                        $validator->errors()->add('lecciones', 
                            "Conflicto de horario: El profesor ya tiene clase en {$recintoExistente} ({$tipoExistente}) de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                        );
                        return; // Salir después del primer conflicto encontrado
                    }
                }
            }
        }
    }

    /**
     * Verificar si dos lecciones tienen conflicto de horario
     */
    private function hayConflictoHorario($leccion1, $leccion2)
    {
        $inicio1 = strtotime($leccion1->hora_inicio);
        $fin1 = strtotime($leccion1->hora_final);
        $inicio2 = strtotime($leccion2->hora_inicio);
        $fin2 = strtotime($leccion2->hora_final);

        // Hay conflicto si los horarios coinciden
        return ($inicio1 < $fin2) && ($fin1 > $inicio2);
    }

    /**
     * Validar conflictos en el mismo recinto: Dos profesores no pueden estar en el mismo recinto al mismo tiempo
     */
    private function validateSameRecintoConflicts($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones)) {
            return;
        }

        // Obtener las lecciones seleccionadas con sus horarios
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)->get();

        // Buscar horarios de otros profesores en el mismo recinto
        $horariosEnRecinto = collect();

        if ($this->tipoHorario === 'fijo') {
            // Para horarios fijos, buscar otros horarios fijos del mismo día
            $horariosEnRecinto = Horario::where('idRecinto', $this->idRecinto)
                ->where('user_id', '!=', $this->user_id)
                ->where('dia', $this->dia)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor'])
                ->get();
        } else {
            // Para horarios temporales, buscar horarios fijos del día correspondiente y otros temporales de la misma fecha
            $diaSemana = date('l', strtotime($this->fecha));
            $diasEspanol = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $diaEnEspanol = $diasEspanol[$diaSemana];

            // Horarios fijos del mismo día de la semana en el mismo recinto
            $horariosFijos = Horario::where('idRecinto', $this->idRecinto)
                ->where('user_id', '!=', $this->user_id)
                ->where('dia', $diaEnEspanol)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor'])
                ->get();

            // Otros horarios temporales de la misma fecha en el mismo recinto
            $horariosTemporales = Horario::where('idRecinto', $this->idRecinto)
                ->where('user_id', '!=', $this->user_id)
                ->where('fecha', $this->fecha)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor'])
                ->get();

            $horariosEnRecinto = $horariosFijos->merge($horariosTemporales);
        }

        // Verificar conflictos de tiempo en el mismo recinto
        foreach ($horariosEnRecinto as $horarioExistente) {
            foreach ($horarioExistente->leccion as $leccionExistente) {
                foreach ($leccionesSeleccionadas as $leccionNueva) {
                    if ($this->hayConflictoHorario($leccionExistente, $leccionNueva)) {
                        $profesorExistente = $horarioExistente->profesor->name ?? 'Profesor desconocido';
                        $tipoExistente = $horarioExistente->tipoHorario == 1 ? 'fijo' : 'temporal';
                        $validator->errors()->add('lecciones', 
                            "Conflicto en el recinto: El profesor {$profesorExistente} ya tiene clase ({$tipoExistente}) en este recinto de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                        );
                        return; // Salir después del primer conflicto encontrado
                    }
                }
            }
        }
    }

    /**
     * Validar conflictos de secciones: La misma sección con la misma subárea no puede estar con el mismo profesor en el mismo recinto en horarios que coincidan
     * Valida tanto horarios fijos como temporales de manera cruzada
     */
    private function validateSameSubareaConflicts($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones)) {
            return;
        }

        // Obtener las lecciones seleccionadas con sus horarios
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)->get();

        // Buscar horarios del mismo profesor en la misma subárea, misma sección y mismo recinto
        $horariosExistentes = collect();

        if ($this->tipoHorario === 'fijo') {
            // Para horarios fijos, buscar otros horarios fijos del mismo día Y horarios temporales del mismo día de la semana
            $horariosFijos = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('idRecinto', $this->idRecinto)
                ->where('user_id', $this->user_id)
                ->where('dia', $this->dia)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'seccion', 'subarea', 'recinto'])
                ->get();

            // Buscar horarios temporales que coincidan con el día de la semana del horario fijo
            $horariosTemporales = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('idRecinto', $this->idRecinto)
                ->where('user_id', $this->user_id)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->whereRaw('DAYNAME(fecha) = ?', [$this->dia === 'Lunes' ? 'Monday' : 
                    ($this->dia === 'Martes' ? 'Tuesday' :
                    ($this->dia === 'Miércoles' ? 'Wednesday' :
                    ($this->dia === 'Jueves' ? 'Thursday' :
                    ($this->dia === 'Viernes' ? 'Friday' :
                    ($this->dia === 'Sábado' ? 'Saturday' : 'Sunday')))))])
                ->with(['leccion', 'seccion', 'subarea', 'recinto'])
                ->get();

            $horariosExistentes = $horariosFijos->merge($horariosTemporales);
        } else {
            // Para horarios temporales, buscar horarios fijos del día correspondiente y otros temporales de la misma fecha
            $diaSemana = date('l', strtotime($this->fecha));
            $diasEspanol = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $diaEnEspanol = $diasEspanol[$diaSemana];

            // Horarios fijos del mismo día de la semana
            $horariosFijos = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('idRecinto', $this->idRecinto)
                ->where('user_id', $this->user_id)
                ->where('dia', $diaEnEspanol)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'seccion', 'subarea', 'recinto'])
                ->get();

            // Otros horarios temporales de la misma fecha
            $horariosTemporales = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('idRecinto', $this->idRecinto)
                ->where('user_id', $this->user_id)
                ->where('fecha', $this->fecha)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->with(['leccion', 'seccion', 'subarea', 'recinto'])
                ->get();

            $horariosExistentes = $horariosFijos->merge($horariosTemporales);
        }

        // Verificar conflictos de tiempo en la misma combinación
        foreach ($horariosExistentes as $horarioExistente) {
            foreach ($horarioExistente->leccion as $leccionExistente) {
                foreach ($leccionesSeleccionadas as $leccionNueva) {
                    if ($this->hayConflictoHorario($leccionExistente, $leccionNueva)) {
                        $seccionExistente = $horarioExistente->seccion->nombre ?? 'Sección desconocida';
                        $subareaExistente = $horarioExistente->subarea->nombre ?? 'Subárea desconocida';
                        $recintoExistente = $horarioExistente->recinto->nombre ?? 'Recinto desconocido';
                        $tipoExistente = $horarioExistente->tipoHorario == 1 ? 'fijo' : 'temporal';
                        $tipoNuevo = $this->tipoHorario === 'fijo' ? 'fijo' : 'temporal';
                        
                        $validator->errors()->add('lecciones', 
                            "Ya tienes asignada la sección {$seccionExistente} de {$subareaExistente} en el recinto {$recintoExistente} ({$tipoExistente}) de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección {$tipoNuevo} de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                        );
                        return; // Salir después del primer conflicto encontrado
                    }
                }
            }
        }
    }

    /**
     * Validar que las lecciones seleccionadas sean consecutivas en tiempo
     */
    private function validateConsecutiveLecciones($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones) || count($this->lecciones) <= 1) {
            return; // Si hay una o ninguna lección, no necesita validación de consecutividad
        }

        // Obtener las lecciones seleccionadas ordenadas por hora de inicio
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)
            ->orderBy('hora_inicio')
            ->get();

        // Verificar que las lecciones sean consecutivas
        for ($i = 0; $i < count($leccionesSeleccionadas) - 1; $i++) {
            $leccionActual = $leccionesSeleccionadas[$i];
            $leccionSiguiente = $leccionesSeleccionadas[$i + 1];

            // La hora final de la lección actual debe ser igual a la hora de inicio de la siguiente
            if ($leccionActual->hora_final !== $leccionSiguiente->hora_inicio) {
                $validator->errors()->add('lecciones', 
                    "Las lecciones seleccionadas deben ser consecutivas. La lección de {$leccionActual->hora_inicio} a {$leccionActual->hora_final} no es consecutiva con la lección de {$leccionSiguiente->hora_inicio} a {$leccionSiguiente->hora_final}."
                );
                return; // Salir después del primer error encontrado
            }
        }
    }

    /**
     * Validar que la misma sección de la misma especialidad no tenga conflictos:
     * 1. Múltiples profesores en horarios coincidentes
     * 2. Mismo profesor con horarios fijos y temporales del mismo día para la misma sección
     */
    private function validateSameSectionSpecialtyConflicts($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones)) {
            return;
        }

        // Obtener las lecciones seleccionadas con sus horarios
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)->get();

        // Buscar TODOS los horarios existentes para la misma sección y especialidad
        $horariosExistentes = collect();

        if ($this->tipoHorario === 'fijo') {
            // Para horarios fijos, buscar otros horarios fijos del mismo día
            $horariosExistentes = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('dia', $this->dia)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor', 'seccion', 'subarea'])
                ->get();
        } else {
            // Para horarios temporales, buscar horarios fijos del día correspondiente y otros temporales de la misma fecha
            $diaSemana = date('l', strtotime($this->fecha));
            $diasEspanol = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $diaEnEspanol = $diasEspanol[$diaSemana];

            // Horarios fijos del mismo día de la semana con la misma sección y subárea
            $horariosFijos = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('dia', $diaEnEspanol)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor', 'seccion', 'subarea'])
                ->get();

            // Otros horarios temporales de la misma fecha con la misma sección y subárea
            $horariosTemporales = Horario::where('idSubarea', $this->idSubarea)
                ->where('idSeccion', $this->idSeccion)
                ->where('fecha', $this->fecha)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->with(['leccion', 'profesor', 'seccion', 'subarea'])
                ->get();

            $horariosExistentes = $horariosFijos->merge($horariosTemporales);
        }

        // Verificar conflictos para cada horario existente
        foreach ($horariosExistentes as $horarioExistente) {
            $esMismoProfesor = $horarioExistente->user_id == $this->user_id;
            $tipoExistente = $horarioExistente->tipoHorario == 1 ? 'fijo' : 'temporal';
            $tipoNuevo = $this->tipoHorario === 'fijo' ? 'fijo' : 'temporal';

            // CASO 1: Mismo profesor, misma sección, mismo día, pero diferente tipo (fijo vs temporal)
            if ($esMismoProfesor && $tipoExistente !== $tipoNuevo) {
                $seccionExistente = $horarioExistente->seccion->nombre ?? 'Sección desconocida';
                $subareaExistente = $horarioExistente->subarea->nombre ?? 'Subárea desconocida';
                
                $validator->errors()->add('general', 
                    "No puedes asignar la misma sección {$seccionExistente} de {$subareaExistente} en un horario {$tipoNuevo} cuando ya tienes un horario {$tipoExistente} para esta sección y especialidad el mismo día."
                );
                return;
            }

            // CASO 2: Verificar conflictos de horarios (tanto para mismo profesor como para diferentes)
            foreach ($horarioExistente->leccion as $leccionExistente) {
                foreach ($leccionesSeleccionadas as $leccionNueva) {
                    if ($this->hayConflictoHorario($leccionExistente, $leccionNueva)) {
                        $profesorExistente = $horarioExistente->profesor->name ?? 'Profesor desconocido';
                        $seccionExistente = $horarioExistente->seccion->nombre ?? 'Sección desconocida';
                        $subareaExistente = $horarioExistente->subarea->nombre ?? 'Subárea desconocida';
                        
                        if ($esMismoProfesor) {
                            // Mismo profesor con conflicto de horarios
                            $validator->errors()->add('lecciones', 
                                "Ya tienes asignada la sección {$seccionExistente} de {$subareaExistente} ({$tipoExistente}) de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                            );
                        } else {
                            // Diferentes profesores con conflicto de horarios
                            $validator->errors()->add('lecciones', 
                                "Conflicto de sección: La sección {$seccionExistente} de {$subareaExistente} ya está asignada al profesor {$profesorExistente} ({$tipoExistente}) de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                            );
                        }
                        return; // Salir después del primer conflicto encontrado
                    }
                }
            }
        }
    }

    /**
     * Validar conflictos entre horarios temporales y fijos
     * Funciona para ambos tipos: fijos vs temporales y temporales vs fijos
     */
    private function validateTemporalFixedConflicts($validator)
    {
        if (!$this->has('lecciones') || empty($this->lecciones)) {
            return;
        }

        // Obtener las lecciones seleccionadas
        $leccionesSeleccionadas = \App\Models\Leccion::whereIn('id', $this->lecciones)->get();

        $horariosFijos = collect();
        $horariosTemporales = collect();

        if ($this->tipoHorario === 'fijo') {
            // Si estamos creando un horario fijo, buscar horarios temporales que coincidan con este día de la semana
            $horariosTemporales = Horario::where('user_id', $this->user_id)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->whereRaw('DAYNAME(fecha) = ?', [$this->dia === 'Lunes' ? 'Monday' : 
                    ($this->dia === 'Martes' ? 'Tuesday' :
                    ($this->dia === 'Miércoles' ? 'Wednesday' :
                    ($this->dia === 'Jueves' ? 'Thursday' :
                    ($this->dia === 'Viernes' ? 'Friday' :
                    ($this->dia === 'Sábado' ? 'Saturday' : 'Sunday')))))])
                ->with(['leccion', 'recinto'])
                ->get();
        } else {
            // Si estamos creando un horario temporal, buscar horarios fijos del día correspondiente
            $diaSemana = date('l', strtotime($this->fecha));
            $diasEspanol = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $diaEnEspanol = $diasEspanol[$diaSemana];

            // Buscar horarios fijos del mismo profesor en el mismo día de la semana
            $horariosFijos = Horario::where('user_id', $this->user_id)
                ->where('dia', $diaEnEspanol)
                ->where('tipoHorario', 1)
                ->where('condicion', 1)
                ->with(['leccion', 'recinto'])
                ->get();

            // Buscar otros horarios temporales del mismo profesor en la misma fecha
            $horariosTemporales = Horario::where('user_id', $this->user_id)
                ->where('fecha', $this->fecha)
                ->where('tipoHorario', 0)
                ->where('condicion', 1)
                ->with(['leccion', 'recinto'])
                ->get();
        }

        $todosLosHorarios = $horariosFijos->merge($horariosTemporales);

        // Verificar conflictos con ambos tipos de horarios
        foreach ($todosLosHorarios as $horarioExistente) {
            foreach ($horarioExistente->leccion as $leccionExistente) {
                foreach ($leccionesSeleccionadas as $leccionNueva) {
                    if ($this->hayConflictoHorario($leccionExistente, $leccionNueva)) {
                        $recintoExistente = $horarioExistente->recinto->nombre ?? 'Recinto desconocido';
                        $tipoExistente = $horarioExistente->tipoHorario == 1 ? 'fijo' : 'temporal';
                        $tipoNuevo = $this->tipoHorario === 'fijo' ? 'fijo' : 'temporal';
                        
                        $validator->errors()->add('lecciones', 
                            "Conflicto de horario: Ya tienes un horario {$tipoExistente} en {$recintoExistente} de {$leccionExistente->hora_inicio} a {$leccionExistente->hora_final}, que coincide con la lección {$tipoNuevo} de {$leccionNueva->hora_inicio} a {$leccionNueva->hora_final}."
                        );
                        return; // Salir después del primer conflicto encontrado
                    }
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'idRecinto.required' => 'El recinto es obligatorio.',
            'idRecinto.integer' => 'El recinto debe ser un valor válido.',
            'idRecinto.exists' => 'El recinto seleccionado no es válido.',
            
            'idSubarea.required' => 'La subárea es obligatoria.',
            'idSubarea.integer' => 'La subárea debe ser un valor válido.',
            'idSubarea.exists' => 'La subárea seleccionada no es válida.',
            
            'idSeccion.required' => 'La sección es obligatoria.',
            'idSeccion.integer' => 'La sección debe ser un valor válido.',
            'idSeccion.exists' => 'La sección seleccionada no es válida.',
            
            'user_id.required' => 'El profesor es obligatorio.',
            'user_id.integer' => 'El profesor debe ser un valor válido.',
            'user_id.exists' => 'El profesor seleccionado no es válido.',
            
            'tipoHorario.required' => 'El tipo de horario es obligatorio.',
            'tipoHorario.string' => 'El tipo de horario debe ser un texto válido.',
            'tipoHorario.in' => 'El tipo de horario debe ser fijo o temporal.',
            
            'fecha.date' => 'La fecha debe tener un formato válido.',
            'fecha.required_if' => 'La fecha es obligatoria para horarios temporales.',
            
            'dia.string' => 'El día debe ser un texto válido.',
            'dia.required_if' => 'El día es obligatorio para horarios fijos.',
            'dia.in' => 'El día seleccionado no es válido.',
            
            'lecciones.required' => 'Debe seleccionar al menos una lección.',
            'lecciones.array' => 'Las lecciones deben ser un listado válido.',
            'lecciones.min' => 'Debe seleccionar al menos una lección.',
            'lecciones.*.integer' => 'Las lecciones seleccionadas deben ser válidas.',
            'lecciones.*.exists' => 'Una o más lecciones seleccionadas no son válidas.',
        ];
    }
}
