<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreInstitucionRequest;
use App\Models\Institucione;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Session\Store;

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
    public function edit(Institucione $institucione): \Illuminate\Contracts\View\View
    {
        
        return view('institucion.edit', compact('institucione'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
