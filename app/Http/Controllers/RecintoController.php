<?php

namespace App\Http\Controllers;
use App\Models\Recinto;
use App\Http\Requests\StoreRecintoRequest;
use App\Http\Requests\UpdateRecintoRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Institucione;
use App\Models\EstadoRecinto;
use App\Models\Llave;
use App\Models\TipoRecinto;
use App\Http\Requests;
use Exception;



class RecintoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $query = Recinto::with(['institucion', 'tipoRecinto', 'estadoRecinto', 'llave'])
            ->where('condicion', 1)
            ->orderBy('nombre');

        if (request('busquedaRecinto')) {
            $busqueda = request('busquedaRecinto');
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%$busqueda%")
                  ->orWhereHas('institucion', function($q2) use ($busqueda) {
                      $q2->where('nombre', 'like', "%$busqueda%");
                  });
            });
        }

        $recintos = $query->get();
        $instituciones = Institucione::all();
        $tiposRecinto = TipoRecinto::all();
        $estadosRecinto = EstadoRecinto::all();
        $llaves = Llave::all();

        return view('recinto.index', compact('recintos', 'instituciones', 'tiposRecinto', 'estadosRecinto', 'llaves'));
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
        //dd($request->validated());
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
