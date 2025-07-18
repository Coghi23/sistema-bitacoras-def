<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateInstitucionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreInstitucionRequest;
use App\Models\Institucione;
use Exception;

class InstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instituciones = Institucione::with('especialidad')->get();
        return view('institucion.index', compact('instituciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('institucion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstitucionRequest $request)
    {
        try{
            
            DB::beginTransaction();
            $institucione = Institucione::create($request->validated());

            DB::commit();

            

            }catch (Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Hubo un problema al guardar la institución.']);
            }

            return redirect()->route('institucion.index')
            ->with('success', 'Institución creada correctamente.');
            

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
    public function edit(Institucione $institucion)
    {
        $instituciones = Institucione::with('especialidad')->get();
        return view('institucion.index', compact('instituciones', 'institucion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstitucionRequest $request, Institucione $institucion)
    {
        try {
            DB::beginTransaction();
            $institucion->update($request->validated());
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar la institución.']);
        }

        return redirect()->route('institucion.index')
        ->with('success', 'Institución actualizada correctamente.');

    }
    
        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $institucion = Institucione::find($id);
        if ($institucion->condicion == 1)
        {
            Institucione::where('id',$institucion->id)
            ->delete();
            $message = 'Institución eliminada';
        } else {
            Institucione::where('id',$institucion->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Institución restaurada';
        }
        return redirect()->route('institucion.index')->with('success', $message);
    }
}
