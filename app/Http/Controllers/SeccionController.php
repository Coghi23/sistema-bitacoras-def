<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Seccione; 
use App\Models\Especialidade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreSeccionRequest;
use App\Http\Requests\UpdateSeccionRequest;
use Exception; 

class SeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Seccione::with(['especialidades' => function($query) {
            $query->where('especialidad.condicion', 1)
                  ->where('especialidad_seccion.condicion', 1);
        }]);
        
        // Búsqueda por nombre de sección
        if ($request->filled('busquedaSeccion')) {
            $busqueda = $request->busquedaSeccion;
            $query->where('nombre', 'like', "%{$busqueda}%");
        }
        
        $secciones = $query->get();
        
        // Obtener todas las especialidades activas para el dropdown
        $especialidades = Especialidade::where('condicion', 1)->get();
        
        return view('seccion.index', compact('secciones', 'especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $especialidades = Especialidade::all();
        return view('seccion.create', compact('especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeccionRequest $request)
    {
        try{
            // Debug: ver qué datos llegan
            \Log::info('Datos del request completo:', $request->all());
            
            DB::beginTransaction();
            
            // Crear la sección
            $seccion = Seccione::create($request->validated());
            \Log::info('Sección creada con ID:', ['id' => $seccion->id]);
            
            // Asociar especialidades si existen
            if ($request->has('especialidades') && is_array($request->especialidades)) {
                $especialidades = array_filter($request->especialidades); // Filtrar valores vacíos
                \Log::info('Especialidades a asociar:', $especialidades);
                if (!empty($especialidades)) {
                    $seccion->especialidades()->attach($especialidades, [
                        'condicion' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    \Log::info('Especialidades asociadas exitosamente');
                }
            } else {
                \Log::warning('No se encontraron especialidades en el request');
            }
            
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al guardar la sección: ' . $e->getMessage()]);
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
        $especialidades = Especialidade::all();
        return view('seccion.edit', compact('seccion', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeccionRequest $request, Seccione $seccion)
    {
        try {
            // Debug: ver qué datos llegan
            \Log::info('Datos del request de actualización:', $request->all());
            
            DB::beginTransaction();
            
            // Actualizar la sección
            $seccion->update($request->validated());
            \Log::info('Sección actualizada con ID:', ['id' => $seccion->id]);
            
            // Sincronizar especialidades
            if ($request->has('especialidades') && is_array($request->especialidades)) {
                $especialidades = array_filter($request->especialidades); // Filtrar valores vacíos
                \Log::info('Especialidades a sincronizar:', $especialidades);
                
                if (!empty($especialidades)) {
                    // Preparar datos para la tabla pivot con condición = 1
                    $pivotData = [];
                    foreach ($especialidades as $especialidadId) {
                        $pivotData[$especialidadId] = [
                            'condicion' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    
                    // Sincronizar especialidades (elimina las anteriores y agrega las nuevas)
                    $seccion->especialidades()->sync($pivotData);
                    \Log::info('Especialidades sincronizadas exitosamente');
                }
            } else {
                \Log::warning('No se encontraron especialidades en el request de actualización');
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un problema al actualizar la sección: ' . $e->getMessage()]);
        }
        
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
