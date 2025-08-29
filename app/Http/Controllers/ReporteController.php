<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function resumenDiario(Request $request)
    {
        $fecha = $request->fecha ? Carbon::parse($request->fecha) : Carbon::today();
        
        $eventos = Evento::with(['bitacora.recinto', 'usuario'])
            ->whereDate('fecha', $fecha)
            ->where('condicion', 1)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('Reporte.resumen-diario', compact('eventos'));
    }
}
