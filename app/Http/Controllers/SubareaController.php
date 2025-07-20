<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Subarea; 
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreSubareaRequest;
use App\Http\Requests\UpdateSubareaRequest;
use Exception; 

class SubareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subareas=Subarea::with('especialidad')->get();
        return view('subarea.index', ['subareas' => $subareas]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subarea.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubareaRequest $request)
    {
        try {
            DB::beginTransaction();
            Subarea::create($request->validated());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return redirect()->route('subarea.index')->with('success', 'Subárea creada correctamente.');
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
    public function edit(Subarea $subarea)
    {
        $subarea->load('especialidad');
         return view('subarea.edit', compact('subarea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubareaRequest $request, Subarea $subarea)
    {
        Subarea::where('id', $subarea->id)
            ->update($request->validated());
        return redirect()->route('subarea.index')->with('success', 'Subárea actualizada correctamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message='';
        $subarea=Subarea::find($id);
        if($subarea->condicion==1){
            Subarea::where('id', $subarea->id)
            ->update([
                'condicion' => 0
            ]);
            $message = 'Subárea eliminada correctamente.';
        }
        else{
            Subarea::where('id', $subarea->id)
            ->update([
                'condicion' => 1
            ]);
            $message = 'Subárea restaurada correctamente.';
        }
        return redirect()->route('subarea.index')->with('success', $message);

    }
}
