<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Recinto;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with(['recinto', 'llave']);
        
        // Filtros por fecha
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        
        if ($request->filled('recinto_id')) {
            $query->where('id_recinto', $request->recinto_id);
        }
        
        $bitacoras = $query->where('condicion', 1)
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);
        
        // Datos para filtros
        $recintos = Recinto::where('condicion', 1)->orderBy('nombre')->get();
        
        return view('reportes.bitacoras', compact('bitacoras', 'recintos'));
    }

    public function resumenDiario()
    {
        // Implementar reporte de resumen diario
        return view('reportes.resumen-diario');
    }
}
