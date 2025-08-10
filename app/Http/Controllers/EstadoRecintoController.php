<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateEstadoRecintoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreEstadoRecintoRequest;
use App\Models\estadoRecinto;
use Exception;
use PgSql\Lob;

class EstadoRecintoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estadoRecintos = estadoRecinto::with('recinto')->get();
        return view('estadoRecinto.index', compact('estadoRecintos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('estadoRecinto.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstadoRecintoRequest $request)
    {
        try{
            
            DB::beginTransaction();
            $estadoRecinto = estadoRecinto::create($request->validated()); // color removed from validated

            DB::commit();

            

            }catch (Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Hubo un problema al guardar el estado de recinto.']);
            }

            return redirect()->route('estadoRecinto.index')
            ->with('success', 'Estado de recinto creado correctamente.');


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
    public function edit(estadoRecinto $estadoRecinto)
    {
        $estadoRecintos = estadoRecinto::with('recinto')->get();
        return view('estadoRecinto.index', compact('estadoRecintos', 'estadoRecinto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEstadoRecintoRequest $request, estadoRecinto $estadoRecinto)
    {
        try {
            DB::beginTransaction();
            $estadoRecinto->update($request->validated()); // color removed from validated
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar el estado de recinto.']);
        }

        return redirect()->route('estadoRecinto.index')
        ->with('success', 'Estado de recinto actualizado correctamente.');

    }
    
        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $estadoRecinto = estadoRecinto::find($id);
        if ($estadoRecinto->condicion == 1)
        {
            estadoRecinto::where('id',$estadoRecinto->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Estado de recinto eliminado';
        } else {
            estadoRecinto::where('id',$estadoRecinto->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Estado de recinto restaurado';
        }
        return redirect()->route('estadoRecinto.index')->with('success', $message);
    }
}
