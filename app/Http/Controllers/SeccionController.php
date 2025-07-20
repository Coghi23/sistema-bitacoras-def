<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Seccione; 
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreSeccionRequest;
use App\Http\Requests\UpdateSeccionRequest;
use Exception; 

class SeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $secciones= Seccione::all();
        return view('seccion.index', ['secciones' => $secciones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seccion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeccionRequest $request)
    {
        try{
            DB::beginTransaction();
            Seccione::create($request->validated());
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
        return redirect()->route('seccion.index')->with('success', 'Sección creada correctamente.');
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
    public function edit(Seccione $seccion)
    {
        return view('seccion.edit', compact('seccion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeccionRequest $request, Seccione $seccion)
    {
        Seccione::where('id', $seccion->id)
            ->update($request->validated());
        return redirect()->route('seccion.index')->with('success', 'Sección actualizada correctamente.');
            
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message='';
        $seccion=Seccione::find($id);
        if($seccion->condicion==1){
            Seccione::where('id', $seccion->id)
            ->update([
                'condicion' => 0
            ]);
            $message = 'Sección eliminada correctamente.';
        }
        else{
            Seccione::where('id', $seccion->id)
            ->update([
                'condicion' => 1
            ]);
            $message = 'Sección restaurada correctamente.';
        }
        return redirect()->route('seccion.index')->with('success', $message);
    }
}
