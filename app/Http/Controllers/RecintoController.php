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
use App\Models\Bitacora;






class RecintoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        $condicion = 1;
        if (request('inactivos')) {
            $condicion = 0;
        }
        $query = Recinto::with(['institucion', 'tipoRecinto', 'estadoRecinto', 'llave'])
            ->where('condicion', $condicion)
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

        return view('Recinto.index', compact('recintos', 'instituciones', 'tiposRecinto', 'estadosRecinto', 'llaves'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('Recinto.create');


    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecintoRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            // Mapear nombres correctos si vienen con mayúsculas
            if (isset($data['tipoRecinto_id'])) {
                $data['tiporecinto_id'] = $data['tipoRecinto_id'];
                unset($data['tipoRecinto_id']);
            }
            if (isset($data['estadoRecinto_id'])) {
                $data['estadorecinto_id'] = $data['estadoRecinto_id'];
                unset($data['estadoRecinto_id']);
            }
            Recinto::create($data);
            DB::commit();
            return redirect()->route('recinto.index')->with('success', 'Recinto creado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear recinto: ' . $e->getMessage());
        }
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
         return view('Recinto.edit', compact('recinto'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecintoRequest $request, Recinto $recinto)
    {
        $data = $request->validated();
        if (isset($data['tipoRecinto_id'])) {
            $data['tiporecinto_id'] = $data['tipoRecinto_id'];
            unset($data['tipoRecinto_id']);
        }
        if (isset($data['estadoRecinto_id'])) {
            $data['estadorecinto_id'] = $data['estadoRecinto_id'];
            unset($data['estadoRecinto_id']);
        }
        Recinto::where('id', $recinto->id)
            ->update($data);
        return redirect()->route('recinto.index')->with('success', 'Recinto actualizada correctamente.');
    }


       
   


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $recinto = Recinto::find($id);

        if ($recinto) {
            // Buscar la bitácora asociada al recinto (usa id_recinto, no recinto_id)
            $bitacora = \App\Models\Bitacora::where('id_recinto', $recinto->id)->first();

            if ($recinto->condicion == 1) {
                Recinto::where('id', $recinto->id)
                    ->update(['condicion' => 0]);
                // Desactivar la bitácora asociada si existe
                if ($bitacora) {
                    $bitacora->condicion = 0;
                    $bitacora->save();
                }
                $message = 'Recinto eliminado correctamente.';
            } else {
                Recinto::where('id', $recinto->id)
                    ->update(['condicion' => 1]);
                // Restaurar la bitácora asociada si existe
                if ($bitacora) {
                    $bitacora->condicion = 1;
                    $bitacora->save();
                }
                $message = 'Recinto restaurado correctamente.';
            }
        } else {
            $message = 'Recinto no encontrado.';
        }

        return redirect()->route('recinto.index')->with('success', $message);
    }
}
