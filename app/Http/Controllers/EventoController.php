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


class EventoController extends Controller
{

    public function index(Request $request)
    {

        $eventos = Evento::with('bitacora', 'usuario', 'seccion', 'subarea', 'horario')->get();
        $bitacoras = Bitacora::all();

        $seccione = Seccione::all();
        $subareas = Subarea::all();
        
        // Filtrar solo los horarios del profesor logueado con relaciones
        $horarios = Horario::with('recinto','subarea','seccion','leccion','profesor')
                           ->where('user_id', auth()->id())
                           ->get();

        // Todas las lecciones asociadas a esos horarios (sin duplicados)
        $lecciones = $horarios->flatMap->leccion->unique('id')->values();

        
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


        return view('Evento.index', compact('eventos', 'bitacoras', 'profesores', 'seccione', 'subareas', 
        'horarios', 'fecha', 'seccion', 'subarea', 'recinto', 
        'horarioSeleccionado', 'lecciones'));
    }

    //funcion de crear
    public function create()
    {

    }

    public function store(StoreEventoRequest $request)
    {
        try {

            $evento = new Evento();

            $evento->id_bitacora = $request->input('id_bitacora');
            $evento->id_seccion = $request->input('id_seccion');
            $evento->id_subarea = $request->input('id_subarea');
            $evento->id_horario = $request->input('id_horario');
            $evento->id_horario_leccion = $request->input('id_horario');
            $evento->user_id = auth()->id();
            $evento->hora_envio = $request->input('hora_envio');
            $evento->fecha = now()->format('Y-m-d');
            $evento->observacion = $request->input('observacion');
            $evento->prioridad = $request->input('prioridad');

            $evento->save();

            return redirect()->route('evento.index')
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

    //metodo update
    public function update(UpdateEventoRequest $request, Evento $evento)
    {
        try {
            DB::beginTransaction();
            $evento->update($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el evento.']);
        }
        return redirect()->route('evento.index')->with('success', 'Evento actualizado correctamente.');
    }

    //metodo destroy, no se utiliza porque no hay razon para eliminar eventos
    public function destroy(string $id)
    {
    }

}
