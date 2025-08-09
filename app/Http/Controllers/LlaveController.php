<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateLlaveRequest;
use BaconQrCode\Encoder\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreLlaveRequest;
use App\Models\Llave;
use Exception;
use PgSql\Lob;

class LlaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $llaves = Llave::with('recinto')->get();
        return view('llave.index', compact('llaves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('llave.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLlaveRequest $request)
    {
        try{
            
            DB::beginTransaction();
            $llave = Llave::create($request->validated());

            DB::commit();

            

            }catch (Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Hubo un problema al guardar la llave.']);
            }

            return redirect()->route('llave.index')
            ->with('success', 'Llave creada correctamente.');


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
    public function edit(Llave $llave)
    {
        $llaves = Llave::with('recinto')->get();
        return view('llave.index', compact('llaves', 'llave'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLlaveRequest $request, Llave $llave)
    {
        try {
            DB::beginTransaction();
            $llave->update($request->validated());
            DB::commit();

            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar la llave.']);
        }

        return redirect()->route('llave.index')
        ->with('success', 'Llave actualizada correctamente.');

    }

    
    
        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $llave = Llave::find($id);
        if ($llave->condicion == 1)
        {
            Llave::where('id',$llave->id)
            ->update(['condicion' => 0
            ]);
            $message = 'Llave eliminada';
        } else {
            Llave::where('id',$llave->id)
            ->update(['condicion' => 1
            ]);
            $message = 'Llave restaurada';
        }
        return redirect()->route('llave.index')->with('success', $message);
    }
}
