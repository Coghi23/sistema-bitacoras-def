<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\TipoLeccion; 
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTipoLeccionRequest;
use App\Http\Requests\UpdateTipoLeccionRequest;
use Exception; 

class TipoLeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoLeccion::with('lecciones');

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('busquedaTipoLeccion')) {
            $busqueda = $request->get('busquedaTipoLeccion');
            $query->where('nombre', 'LIKE', '%' . $busqueda . '%');
        }

        $tipoLecciones = $query->get();
        
        return view('TipoLeccion.index', compact('tipoLecciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('TipoLeccion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoLeccionRequest $request)
    {
        try{
            
            DB::beginTransaction();
            $tipoLeccion = TipoLeccion::create($request->validated());

            DB::commit();

            

            }catch (Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Hubo un problema al guardar el tipo de lección.']);
            }

            return redirect()->route('tipoLeccion.index')
            ->with('success', 'Tipo de lección creado correctamente.');


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
    public function edit(TipoLeccion $tipoLeccion)
    {
    $tipoLecciones = TipoLeccion::with('lecciones')->get();
        return view('TipoLeccion.index', compact('tipoLecciones', 'tipoLeccion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoLeccionRequest $request, TipoLeccion $tipoLeccion)
    {
        try {
            DB::beginTransaction();
            $tipoLeccion->update($request->validated());
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar el tipo de lección.']);
        }

        return redirect()->route('tipoLeccion.index')
        ->with('success', 'Tipo de lección actualizado correctamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $tipoLeccion = TipoLeccion::find($id);
        if ($tipoLeccion->condicion == 1)
        {
            TipoLeccion::where('id',$tipoLeccion->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Tipo de lección eliminado';
        } else {
            TipoLeccion::where('id',$tipoLeccion->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Tipo de lección restaurado';
        }
        return redirect()->route('tipoLeccion.index')->with('success', $message);
    }
}
