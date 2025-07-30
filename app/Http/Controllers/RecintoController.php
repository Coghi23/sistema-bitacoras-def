<?php

namespace App\Http\Controllers;
use App\Models\Recinto;
use App\Http\Requests\StoreRecintoRequest;
use App\Http\Requests\UpdateRecintoRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Institucione;
use App\Http\Requests;
use Exception;



class RecintoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
        // Obtiene los valores enviados por solicitud HTTP con nombre 'tipo' y 'estado'
        $tipo = request('tipo');
        $estado = request('estado');

        // Inicia una consulta para obtener registros de la tabla 'recintos' junto con su relación 'institucion'
        $recintos = Recinto::with('institucion')
            // Si se proporcionó un valor de $tipo, aplica un filtro WHERE para 'tipo'
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('tipo', $tipo);
            })
            // Si se proporcionó un valor de $estado, aplica un filtro WHERE para 'estado'
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            // Ejecuta la consulta y obtiene todos los resultados
            ->get();

        // Obtiene todas las instituciones (sin filtros)
        $instituciones = Institucione::all();

        // Retorna la vista 'recinto.index', pasando las variables 'recintos' e 'instituciones'
        return view('recinto.index', compact('recintos', 'instituciones'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('recinto.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecintoRequest $request)
    {
        try {
            DB::beginTransaction();
            Recinto::create($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return redirect()->route('recinto.index')->with('success', 'Recinto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recinto $recinto)
    {
         $recinto->load('institucion');
         return view('recinto.edit', compact('recinto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecintoRequest $request, Recinto $recinto)
    {
        Recinto::where('id', $recinto->id)
            ->update($request->validated());
        return redirect()->route('recinto.index')->with('success', 'Recinto actualizada correctamente.');
    }

        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $message='';
        $recinto=Recinto::find($id);
        if($recinto->condicion==1){
            Recinto::where('id', $recinto->id)
            ->update([
                'condicion' => 0
            ]);
            $message = 'Recinto eliminado correctamente.';
        }
        else{
            Recinto::where('id', $recinto->id)
            ->update([
                'condicion' => 1
            ]);
            $message = 'Recinto restaurado correctamente.';
        }
        return redirect()->route('recinto.index')->with('success', $message);
    }
}
