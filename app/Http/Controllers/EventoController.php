<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEventoRequest;
use App\Models\Profesor;
use App\Models\Bitacora;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreEventoRequest;
use App\Models\Evento;
use App\Models\Seccione;
use App\Models\Subarea;
use App\Models\Horario;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class EventoController extends Controller
{

    public function index(Request $request)
    {
        $eventos = Evento::with([
            'bitacora',
            'usuario',
            'seccion',
            'subarea.especialidad',
            'horario.recinto.institucion'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            try {
                $view = view('Evento.partials.eventos-lista', compact('eventos'))->render();
                return response()->json([
                    'success' => true,
                    'html' => $view
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los eventos',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return view('Evento.index', compact('eventos'));
    }

    public function index_soporte(Request $request)
    {
        $eventos = Evento::with([
            'bitacora',
            'usuario',
            'seccion',
            'subarea.especialidad',
            'horario.recinto.institucion'
        ])
            ->where('condicion', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            try {
                $html = view('Evento.index.soporte', compact('eventos'))->renderSections()['content'];
                return response()->json([
                    'success' => true,
                    'hasNewData' => true,
                    'html' => $html,
                    'timestamp' => $eventos->max('updated_at')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los eventos'
                ], 500);
            }
        }

        return view('Evento.index_soporte', compact('eventos'));
    }

        public function index_profesor(Request $request)
    {
        $eventos = Evento::with([
            'bitacora',
            'usuario',
            'seccion',
            'subarea.especialidad',
            'horario.recinto.institucion'
        ])
            ->orderBy('created_at', 'desc')
            ->get();


        $bitacoras = Bitacora::all();

        $seccione = Seccione::all();
        $subareas = Subarea::all();

        // Filtrar solo los horarios del profesor logueado con relaciones
        $horarios = Horario::with(['recinto', 'subarea', 'seccion', 'leccion'])
            ->where('user_id', auth()->id())
            ->get();

        // Obtener todas las lecciones disponibles
        $lecciones = $horarios->flatMap(function ($horario) {
            return $horario->leccion->map(function ($leccion) use ($horario) {
                $leccion->horario_data = $horario;
                return $leccion;
            });
        })->unique('id');

        // Obtener datos del horario seleccionado si existe
        $horarioSeleccionado = null;
        if ($request->filled('leccion')) {
            $horarioSeleccionado = $horarios->where('id', $request->get('leccion'))->first();
        }

        // Obtener la fecha del primer horario o horario seleccionado
        $fecha = $horarioSeleccionado ? $horarioSeleccionado->fecha : ($horarios->first() ? $horarios->first()->fecha : null);

        // Obtener datos dinámicos basados en la selección
        $seccion = $horarioSeleccionado && $horarioSeleccionado->seccion ? $horarioSeleccionado->seccion->nombre : '';
        $subarea = $horarioSeleccionado && $horarioSeleccionado->subarea ? $horarioSeleccionado->subarea->nombre : '';
        $recinto = $horarioSeleccionado && $horarioSeleccionado->recinto ? $horarioSeleccionado->recinto->nombre : '';

        // Obtener la bitácora asociada al recinto seleccionado
        $bitacoraId = $horarioSeleccionado && $horarioSeleccionado->recinto ?
            Bitacora::where('recinto_id', $horarioSeleccionado->recinto->id)->value('id') : null;


        if ($request->ajax()) {
            try {
                $html = view('Evento.index.soporte', compact('eventos'))->renderSections()['content'];
                return response()->json([
                    'success' => true,
                    'hasNewData' => true,
                    'html' => $html,
                    'timestamp' => $eventos->max('updated_at')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los eventos'
                ], 500);
            }
        }

        return view('Evento.index_profesor', compact('eventos', 'bitacoras', 'seccione', 
        'subareas', 'horarios', 'lecciones', 'horarioSeleccionado', 'fecha', 'seccion', 'subarea', 'recinto', 'bitacoraId'));
    }

    //funcion de crear
    public function create(Request $request)
    {
        $eventos = Evento::with('bitacora', 'usuario', 'seccion', 'subarea', 'horario')->get();
        $bitacoras = Bitacora::all();
        $seccione = Seccione::all();
        $subareas = Subarea::all();

        // Obtener todos los usuarios con rol profesor
        $profesores = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();

        // Si se recibe id_bitacora, filtrar por el horario de esa bitácora
        $lecciones = collect();
        $horarioSeleccionado = null;
        $fecha = null;
        $seccion = '';
        $subarea = '';
        $recinto = '';
        $bitacoraId = null;
        $horarios = collect();

        if ($request->filled('id_bitacora')) {
            $bitacoraId = $request->get('id_bitacora');
            $bitacora = Bitacora::find($bitacoraId);
            if ($bitacora && $bitacora->id_recinto) {
                // Buscar el horario que tenga ese recinto
                $horarioSeleccionado = Horario::with(['recinto', 'subarea', 'seccion', 'leccion'])
                    ->where('idRecinto', $bitacora->id_recinto)
                    ->first();
                if ($horarioSeleccionado) {
                    $horarios = collect([$horarioSeleccionado]);
                    // Asignar horario_data a cada lección
                    $lecciones = $horarioSeleccionado->leccion->map(function($leccion) use ($horarioSeleccionado) {
                        $leccion->horario_data = $horarioSeleccionado;
                        return $leccion;
                    });
                    $fecha = $horarioSeleccionado->fecha;
                    $seccion = $horarioSeleccionado->seccion ? $horarioSeleccionado->seccion->nombre : '';
                    $subarea = $horarioSeleccionado->subarea ? $horarioSeleccionado->subarea->nombre : '';
                    $recinto = $horarioSeleccionado->recinto ? $horarioSeleccionado->recinto->nombre : '';
                }
            }
        }

        return view('Evento.create', compact(
            'eventos',
            'bitacoras',
            'profesores',
            'seccione',
            'subareas',
            'horarios',
            'fecha',
            'seccion',
            'subarea',
            'recinto',
            'horarioSeleccionado',
            'lecciones',
            'bitacoraId'
        ));
    }

    public function store(StoreEventoRequest $request)
    {
        // Recibimos el id de lección y el id de horario seleccionados
        $idLeccion = $request->id_leccion;
        $idHorario = $request->id_horario;
        $leccion = \App\Models\Leccion::find($idLeccion);
        $horario = Horario::with('recinto')->find($idHorario);
        if (!$leccion) {
            return back()->withErrors(['error' => 'La lección seleccionada no existe.']);
        }
        if (!$horario) {
            return back()->withErrors(['error' => 'El horario seleccionado no existe.']);
        }
        // Buscar el id de la fila pivote horario_leccion
        $idHorarioLeccion = \DB::table('horario_leccion')
            ->where('idHorario', $idHorario)
            ->where('idLeccion', $idLeccion)
            ->value('id');
        if (!$idHorarioLeccion) {
            return back()->withErrors(['error' => 'No existe relación entre el horario y la lección seleccionados.']);
        }
        DB::beginTransaction();
        try {
            $evento = new Evento();
            $bitacora = Bitacora::where('id_recinto', $horario->recinto->id)->first();
            if (!$bitacora) {
                throw new Exception('No se encontró una bitácora para el recinto de este horario.');
            }
            $evento->id_bitacora = $bitacora->id;
            $evento->id_seccion = $request->id_seccion;
            $evento->id_subarea = $request->id_subarea;
            $evento->id_horario = $horario->id;
            $evento->id_horario_leccion = $idHorarioLeccion;
            $evento->user_id = auth()->id();
            $evento->hora_envio = now()->format('H:i:s');
            $evento->fecha = now();
            $evento->observacion = $request->observacion;
            $evento->prioridad = $request->prioridad;
            $evento->confirmacion = false;
            $evento->condicion = 1;
            $evento->save();
            DB::commit();
            return redirect()->route('evento.index_profesor')
                ->with('success', 'Evento guardado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al guardar el evento. ' . $e->getMessage()]);
        }
    }


    //metodo editar
    public function edit(Evento $evento)
    {
        $bitacoras = Bitacora::all();
        $profesores = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();

        $evento->load('bitacora', 'profesor');

        return view('Evento.edit', compact('evento', 'bitacoras', 'profesores'));
    }


    public function loadEventos(Request $request)
    {
        try {
            $eventos = Evento::with([
                'bitacora',
                'usuario',
                'seccion',
                'subarea.especialidad',
                'horario.recinto.institucion'
            ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get the latest update timestamp from all events
            $latestUpdate = $eventos->max('updated_at');
            $currentTimestamp = $request->query('timestamp');

            // Check if there are any changes
            $hasChanges = !$currentTimestamp || $latestUpdate > $currentTimestamp;

            if (!$hasChanges) {
                return response()->json([
                    'success' => true,
                    'hasNewData' => false
                ]);
            }

            $html = view('Evento.index', compact('eventos'))->renderSections()['content'];

            return response()->json([
                'success' => true,
                'hasNewData' => true,
                'html' => $html,
                'timestamp' => $latestUpdate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los eventos'
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            Log::info('Intentando actualizar evento', [
                'id' => $id,
                'request_all' => $request->all(),
                'raw_input' => file_get_contents('php://input'),
                'headers' => $request->headers->all()
            ]);

            $evento = Evento::findOrFail($id);

            // Validar estado obligatorio y correcto
            $rules = [
                'estado' => 'required|in:en_espera,en_proceso,completado',
                'observacion' => 'sometimes|nullable|string',
                'prioridad' => 'sometimes|nullable|in:alta,media,regular,baja'
            ];
            $validated = $request->validate($rules);

            Log::info('Datos validados para update', $validated);

            $evento->estado = $validated['estado'];
            if (array_key_exists('prioridad', $validated)) {
                $evento->prioridad = $validated['prioridad'];
            }
            if (array_key_exists('observacion', $validated)) {
                $evento->observacion = $validated['observacion'];
            }

            $evento->save();

            Log::info('Evento actualizado correctamente', [
                'evento_id' => $evento->id,
                'nuevo_estado' => $evento->estado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento actualizado correctamente.',
                'estado' => $evento->estado
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('Validación fallida al actualizar evento', [
                'id' => $id,
                'errors' => $ve->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos para actualizar el evento.',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar evento', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $message = '';
        $evento = Evento::find($id);
        if ($evento->condicion == 1)
        {
            Evento::where('id',$evento->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Evento eliminado';
        } else {
            Evento::where('id',$evento->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Evento restaurado';
        }
        return redirect()->route('evento.index_profesor')->with('success', $message);
    }

    // Add these two new methods
    public function loadEventosProfesor(Request $request)
    {
        try {
            $user = auth()->user();
            
            $eventos = Evento::with([
                'bitacora',
                'usuario',
                'seccion',
                'subarea.especialidad',
                'horario.recinto.institucion'
            ])
            ->where('user_id', $user->id)
            ->where('condicion', 1)
            ->orderBy('created_at', 'desc')
            ->get();

            $latestUpdate = $eventos->max('updated_at');
            $currentTimestamp = $request->query('timestamp');
            $hasChanges = !$currentTimestamp || $latestUpdate > $currentTimestamp;

            if (!$hasChanges) {
                return response()->json([
                    'success' => true,
                    'hasNewData' => false
                ]);
            }

            $html = view('Evento.index_profesor', compact('eventos'))->renderSections()['content'];

            return response()->json([
                'success' => true,
                'hasNewData' => true,
                'html' => $html,
                'timestamp' => $latestUpdate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los eventos'
            ], 500);
        }
    }

    public function loadEventosSoporte(Request $request)
    {
        try {
            $eventos = Evento::with([
                'bitacora',
                'usuario',
                'seccion',
                'subarea.especialidad',
                'horario.recinto.institucion'
            ])
            ->where('condicion', 1)
            ->orderBy('created_at', 'desc')
            ->get();

            $latestUpdate = $eventos->max('updated_at');
            $currentTimestamp = $request->query('timestamp');
            $hasChanges = !$currentTimestamp || $latestUpdate > $currentTimestamp;

            if (!$hasChanges) {
                return response()->json([
                    'success' => true,
                    'hasNewData' => false
                ]);
            }

            $html = view('Evento.index_soporte', compact('eventos'))->renderSections()['content'];

            return response()->json([
                'success' => true,
                'hasNewData' => true,
                'html' => $html,
                'timestamp' => $latestUpdate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los eventos'
            ], 500);
        }
    }
}
