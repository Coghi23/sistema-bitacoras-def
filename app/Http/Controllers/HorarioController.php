<?php

namespace App\Http\Controllers;
use App\Models\Horario;
use App\Models\Leccion;
use Illuminate\Http\Request;
use App\Models\Recinto;
use App\Models\Subarea;
use App\Models\User;
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

        $horarios = Horario::with('recinto', 'subarea', 'seccion', 'profesor', 'leccion')->get();
        $recintos = Recinto::all();
        $subareas = Subarea::all();
        $secciones = Seccione::all();
    $lecciones = Leccion::where('condicion', 1)->get();
        $profesores = User::whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        })->get();
        return view('Horario.index', compact('horarios', 'recintos', 'subareas', 'secciones', 'profesores', 'lecciones'));
    }

    //metodo crear para ir a la vista
    public function create()
    {
        $recintos = Recinto::all();
        $subareas = Subarea::all();
        $secciones = Seccione::all();
        $profesores = User::whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        })->get();
        return view('Horario.create', compact('recintos', 'subareas', 'secciones', 'profesores'));
    }


    //metodo store
    public function store(StoreHorarioRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $tipoHorario = $request->tipoHorario === 'fijo' ? 1 : 0;
            
            // Crear UN SOLO horario
            $horario = Horario::create([
                'idRecinto' => $request->idRecinto,
                'idSubarea' => $request->idSubarea,
                'idSeccion' => $request->idSeccion,
                'user_id' => $request->user_id,
                'tipoHorario' => $tipoHorario,
                'fecha' => $request->tipoHorario === 'temporal' ? $request->fecha : null,
                'dia' => $request->tipoHorario === 'fijo' ? $request->dia : null,
                'condicion' => 1
            ]);
            
            // Asociar las lecciones usando la tabla pivot
            if ($request->has('lecciones') && is_array($request->lecciones)) {
                $leccionesData = [];
                foreach ($request->lecciones as $leccionId) {
                    $leccionesData[$leccionId] = ['condicion' => 1];
                }
                $horario->leccion()->attach($leccionesData);
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al crear el horario: ' . $e->getMessage()]);
        }
        return redirect()->route('horario.index')->with('success', 'Horario creado correctamente.');
    }

    //metodo editar
    public function edit(Horario $horario)
    {
        $recintos = Recinto::all();
        $subareas = Subarea::all();
        $secciones = Seccione::all();
    $lecciones = Leccion::where('condicion', 1)->get();
        $profesores = User::whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        })->get();
        $horario->load('recinto', 'subarea', 'seccion', 'profesor', 'leccion');

        return view('Horario.edit', compact('horario', 'recintos', 'subareas', 'secciones', 'profesores', 'lecciones'));
    }


    //metodo update
    public function update(UpdateHorarioRequest $request, Horario $horario)
    {
        try {
            DB::beginTransaction();
            
            $tipoHorario = $request->tipoHorario === 'fijo' ? 1 : 0;
            
            // Actualizar los datos del horario
            $horario->update([
                'idRecinto' => $request->idRecinto,
                'idSubarea' => $request->idSubarea,
                'idSeccion' => $request->idSeccion,
                'user_id' => $request->user_id,
                'tipoHorario' => $tipoHorario,
                'fecha' => $request->tipoHorario === 'temporal' ? $request->fecha : null,
                'dia' => $request->tipoHorario === 'fijo' ? $request->dia : null,
            ]);
            
            // Sincronizar las lecciones (esto eliminará las anteriores y agregará las nuevas)
            if ($request->has('lecciones') && is_array($request->lecciones)) {
                $leccionesData = [];
                foreach ($request->lecciones as $leccionId) {
                    $leccionesData[$leccionId] = ['condicion' => 1];
                }
                $horario->leccion()->sync($leccionesData);
            } else {
                // Si no se seleccionaron lecciones, eliminar todas las asociaciones
                $horario->leccion()->detach();
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el horario: ' . $e->getMessage()]);
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
