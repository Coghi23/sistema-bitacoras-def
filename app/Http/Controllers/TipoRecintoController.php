<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateTipoRecintoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreTipoRecintoRequest;
use App\Models\TipoRecinto;
use Exception;
use PgSql\Lob;
use Illuminate\Http\Request;

class TipoRecintoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $query = TipoRecinto::with('recintos');

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('busquedaTipoRecinto')) {
            $busqueda = $request->get('busquedaTipoRecinto');
            $query->where('nombre', 'LIKE', '%' . $busqueda . '%');
        }

        $tipoRecintos = $query->get();
        
        return view('tipoRecinto.index', compact('tipoRecintos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipoRecinto.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoRecintoRequest $request)
    {
        try{
            
            DB::beginTransaction();
            $tipoRecinto = TipoRecinto::create($request->validated());

            DB::commit();

            

            }catch (Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Hubo un problema al guardar el tipo de recinto.']);
            }

            return redirect()->route('tipoRecinto.index')
            ->with('success', 'Tipo de recinto creado correctamente.');


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
    public function edit(TipoRecinto $tipoRecinto)
    {
    $tipoRecintos = TipoRecinto::with('recintos')->get();
        return view('tipoRecinto.index', compact('tipoRecintos', 'tipoRecinto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoRecintoRequest $request, TipoRecinto $tipoRecinto)
    {
        try {
            DB::beginTransaction();
            $tipoRecinto->update($request->validated());
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar el tipo de recinto.']);
        }

        return redirect()->route('tipoRecinto.index')
        ->with('success', 'Tipo de recinto actualizado correctamente.');

    }
    
        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $tipoRecinto = TipoRecinto::find($id);
        if ($tipoRecinto->condicion == 1)
        {
            TipoRecinto::where('id',$tipoRecinto->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Tipo de recinto eliminado';
        } else {
            TipoRecinto::where('id',$tipoRecinto->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Tipo de recinto restaurado';
        }
        return redirect()->route('tipoRecinto.index')->with('success', $message);
    }
}
