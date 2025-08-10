<?php

namespace App\Http\Controllers;
use App\Models\Bitacora;
use App\Models\Recinto;
use App\Models\Seccione;
use App\Models\Subarea;
use App\Models\Horario;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bitacoras = Bitacora::with('recinto','usuario','seccione','subarea','horario','evento')->get();
        $recintos = Recinto::all();
        $seccione = Seccione::all();
        $subareas = Subarea::all();
        $horarios = Horario::all();
        $eventos = Evento::all();
        
        // Obtener todos los usuarios con rol profesor
        $profesores = User::whereHas('roles', function($query) {
            $query->where('name', 'profesor');
        })->get();

        // Obtener la fecha del primer horario (ajusta según tu lógica)
        $fecha = $horarios->first() ? $horarios->first()->fecha : null;
        $seccion = $seccione->first() ? $seccione->first()->nombre : '';
        $subarea = $subareas->first() ? $subareas->first()->nombre : '';

        return view('bitacora.index', compact(
            'bitacoras', 'recintos', 'profesores', 'seccione', 'subareas', 'horarios', 'eventos', 'fecha', 'seccion', 'subarea'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bitacora = new Bitacora();
        // ...asigna otros campos...
        $bitacora->hora_envio = $request->input('hora_envio');
        $bitacora->fecha = $request->input('fecha');
        // ...asigna otros campos...
        $bitacora->save();
        // ...existing code...
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
