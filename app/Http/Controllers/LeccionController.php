<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Leccion; 
use App\Models\TipoLeccion;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreLeccionRequest;
use App\Http\Requests\UpdateLeccionRequest;
use Exception; 

class LeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Leccion::with('tipoLeccion')->where('condicion', 1);

        // Búsqueda por nombre de lección o tipo de lección
        if ($request->filled('busquedaLeccion')) {
            $busqueda = $request->busquedaLeccion;
            $query->where(function($q) use ($busqueda) {
                $q->where('leccion', 'like', "%{$busqueda}%")
                  ->orWhereHas('tipoLeccion', function($q2) use ($busqueda) {
                      $q2->where('nombre', 'like', "%{$busqueda}%");
                  });
            });
        }

        $lecciones = $query->get();
        $tipoLecciones = TipoLeccion::all();
        return view('Leccion.index', compact('lecciones', 'tipoLecciones'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leccion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeccionRequest $request)
    {
         try {
            DB::beginTransaction();
            $leccion = Leccion::create($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al guardar la lección: ' . $e->getMessage()]);
        }
        return redirect()->route('leccion.index')->with('success', 'Lección creada correctamente.');
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
    public function edit(Leccion $leccion)
    {
        $leccion->load('tipoLeccion');
        return view('leccion.edit', compact('leccion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeccionRequest $request, Leccion $leccion)
    {
         try {
            DB::beginTransaction();
            $leccion->update($request->validated());
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar la lección.']);
        }

        return redirect()->route('leccion.index')
        ->with('success', 'Lección actualizada correctamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $leccion = Leccion::find($id);
        if ($leccion->condicion == 1)
        {
            Leccion::where('id',$leccion->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Lección eliminada';
        } else {
            Leccion::where('id',$leccion->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Lección restaurada';
        }
        return redirect()->route('leccion.index')->with('success', $message);
    
    }
}
