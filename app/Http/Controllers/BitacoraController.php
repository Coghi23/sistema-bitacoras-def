<?php

namespace App\Http\Controllers;
use App\Models\Bitacora;
use App\Models\Recinto;
use App\Models\Seccione;
use App\Models\Subarea;
use App\Models\Horario;
use App\Models\Evento;
use App\Models\Leccion;
use App\Models\User;
use App\Http\Requests\StoreBitacoraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $bitacoras = Bitacora::with('recinto','usuario','seccion','subarea','horario','evento')->get();
        $recintos = Recinto::all();
        $seccione = Seccione::all();
        $subareas = Subarea::all();
        
        // Filtrar solo los horarios del profesor logueado con relaciones
        $horarios = Horario::with('recinto','subarea','seccion','leccion','profesor')
                           ->where('user_id', auth()->id())
                           ->get();

        // Todas las lecciones asociadas a esos horarios (sin duplicados)
        $lecciones = $horarios->flatMap->leccion->unique('id')->values();
        
        $eventos = Evento::all();
        
        // Obtener todos los usuarios con rol profesor
        $profesores = User::whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        })->get();

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

        return view('Bitacora.index', compact(
        'bitacoras', 'recintos', 'profesores', 'seccione', 'subareas', 
        'horarios', 'eventos', 'fecha', 'seccion', 'subarea', 'recinto', 
        'horarioSeleccionado', 'lecciones'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBitacoraRequest $request)
    {
        try {
            $bitacora = new Bitacora();
            
            $bitacora->id_recinto = $request->input('id_recinto');
            $bitacora->id_seccion = $request->input('id_seccion');
            $bitacora->id_subarea = $request->input('id_subarea');
            $bitacora->id_horario = $request->input('id_horario');
            $bitacora->id_horario_leccion = $request->input('id_horario');
            $bitacora->user_id = auth()->id();
            $bitacora->hora_envio = $request->input('hora_envio');
            $bitacora->fecha = now()->format('Y-m-d');
            
            $bitacora->save();
            
            return redirect()->route('bitacora.index')
                           ->with('success', 'Bitácora enviada correctamente');
            
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al enviar la bitácora')
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

    

    