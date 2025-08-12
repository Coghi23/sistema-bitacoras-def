<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Profesor;
use App\Models\Bitacora;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreEventoRequest;
use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;


class EventoController extends Controller
{

    public function index()
    {

        $eventos = Evento::with('bitacora', 'profesor')->get();
        $bitacoras = Bitacora::all();
        $profesores = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();

        return view('evento.index', compact('eventos', 'bitacoras', 'profesores'));
    }

    //funcion de crear
    public function create()
    {
        $bitacoras = Bitacora::all();
        $profesores = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();
        return view('evento.create', compact('bitacoras', 'profesores'));
    }

    public function store(StoreEventoRequest $request)
    {
        try {

            DB::beginTransaction();

            Evento::create([
                'idBitacora' => $request->input('idBitacora'),
                'user_id' => $request->input('user_id'),
                'fecha' => $request->input('fecha'),
                'observacion' => $request->input('observacion'),
                'prioridad' => $request->input('prioridad'),
                'confirmacion' => $request->input('confirmacion'),
                'condicion' => $request->input('condicion')
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al guardar el evento. ' . $e->getMessage()]);
        }

        return redirect()->route('evento.index')
            ->with('success', 'Evento guardado correctamente.');
    }

    //metodo editar
    public function edit(Evento $evento)
    {
        $bitacoras = Bitacora::all();
        $profesores = User::whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        })->get();

        $evento->load('bitacora', 'profesor');

        return view('evento.edit', compact('evento', 'bitacoras', 'profesores'));
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

    //metodo destroy
    public function destroy(string $id)
    {
        $message = "";
        $evento = Evento::find($id);
        if (!$evento) {
            return redirect()->route('evento.index')->withErrors(['error' => 'Evento no encontrado.']);
        }
        if ($evento->condicion == 1) {
            Evento::where('id', $evento->id)
                ->update(['condicion' => 0]);
            $message = 'Evento eliminado correctamente.';
        } else {
            Evento::where('id', $evento->id)
                ->update(['condicion' => 1]);
            $message = 'Evento restaurado correctamente.';
        }
        return redirect()->route('evento.index')->with('success', $message);
    }

}
