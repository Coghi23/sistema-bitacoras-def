<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use App\Http\Requests\StoreEspecialidadRequest;
use App\Http\Requests\UpdateEspecialidadRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Institucione;
use Exception;


class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $especialidades = Especialidade::with('institucion')->get();
        $instituciones = Institucione::where('condicion', 1)->get();

        return view('especialidad.index', compact('especialidades', 'instituciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEspecialidadRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Crear la especialidad
            $especialidad = Especialidade::create([
                'nombre' => $request->input('nombre'),
                'id_institucion' => $request->input('id_institucion'),
                'condicion' => 1 // Activo por defecto
            ]);

            DB::commit();

            return redirect()->route('especialidad.index')
                ->with('success', 'Especialidad creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al guardar la especialidad.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $especialidad = Especialidade::findOrFail($id);
        $especialidades = Especialidade::with('institucion')->get();
        $instituciones = Institucione::where('condicion', 1)->get();

        return view('especialidad.index', compact('especialidades', 'instituciones', 'especialidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEspecialidadRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            
            $especialidad = Especialidade::findOrFail($id);
            
            $especialidad->update([
                'nombre' => $request->input('nombre'),
                'id_institucion' => $request->input('id_institucion')
            ]);

            DB::commit();

            return redirect()->route('especialidad.index')
                ->with('success', 'Especialidad actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar la especialidad.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
     {
        $message = '';
        $especialidad = Especialidade::find($id);
        if ($especialidad->condicion == 1)
        {
            Especialidade::where('id',$especialidad->id)
            ->update(['condicion' => 0]);
            $message = 'Especialidad eliminada';
        } else {
            Especialidade::where('id',$especialidad->id)
            ->update(['condicion' => 1]);
            $message = 'Especialidad restaurada';
        }
        return redirect()->route('especialidad.index')->with('success', $message);
    }
}
