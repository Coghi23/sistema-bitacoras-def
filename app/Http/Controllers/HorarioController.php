<?php

namespace App\Http\Controllers;
use App\Models\Horario;
use Illuminate\Http\Request;
use App\Models\Recinto;
use App\Models\Subarea;
use App\Models\Seccion;
use App\Models\Profesor;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Seccione;
use Exception;

class HorarioController extends Controller
{
    //metodo index
    public function index()
    {
        $horarios = Horario::with('recinto', 'subAreaSeccion', 'profesor')->get();
        return view('horario.index', compact('horarios'));
    }

    //metodo crear para ir a la vista
    public function create()
    {
        $recintos = Recinto::all();
        $subareas = Subarea::all();
        $secciones = Seccione::all();
        $profesores = Profesor::all();
        return view('horario.create', compact('recintos', 'subareas', 'profesores'));
    }


    //metodo store
    public function store(StoreHorarioRequest $request)
    {
        try {
            DB::beginTransaction();
            Horario::create($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al crear el horario.']);
        }
        return redirect()->route('horario.index')->with('success', 'Horario creado correctamente.');
    }

    //metodo editar
    public function edit(Horario $horario)
    {
        $recintos = Recinto::all();
        $subareas = Subarea::all();
        $secciones = Seccione::all();
        $profesores = Profesor::all();
        $horario->load('recinto', 'subAreaSeccion', 'profesor');

        return view('horario.edit', compact('horario', 'recintos', 'subareas', 'secciones', 'profesores'));
    }


    //metodo updAate
    public function update(UpdateHorarioRequest $request, Horario $horario)
    {
        try {
            DB::beginTransaction();
            $horario->update($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el horario.']);
        }
        return redirect()->route('horario.index')->with('success', 'Horario actualizado correctamente.');
    }

    //metodo destroy
    public function destroy(string $id)
    {
        $message ="";
        $horario = Horario::find($id);
        if ($horario->condicion == 1) {
            Horario::where('id', $horario->id)
                ->update(['condicion' => 0]);
            $message = 'Horario eliminado correctamente.';
        } else {
            Horario::where('id', $horario->id)
                ->update(['condicion' => 1]);
            $message = 'Horario restaurado correctamente.';
        }
        return redirect()->route('horario.index')->with('success', $message);
    }
}
