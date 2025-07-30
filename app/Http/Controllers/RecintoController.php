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
    $tipo = request('tipo');
    $recintos = Recinto::with('institucion')
        ->when($tipo, function ($query) use ($tipo) {
            $query->where('tipo', $tipo);
        })
        ->get();
    $instituciones = Institucione::all();
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
