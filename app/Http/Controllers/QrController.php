<?php

namespace App\Http\Controllers;

use App\Models\Llave;
use App\Models\Bitacora;
use App\Models\Recinto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrController extends Controller
{
    /**
     * Escanear QR y cambiar estado de llave
     */
    public function escanearQr(Request $request)
    {
        try {
            $llaveId = $request->input('llave_id');
            $llave = Llave::find($llaveId);
            
            if (!$llave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Llave no encontrada'
                ], 404);
            }

            Log::info('Simulando escaneo QR', [
                'llave_id' => $llaveId,
                'estado_llave_actual' => $llave->estado,
                'usuario' => Auth::id() ?? 'Sistema'
            ]);

            // Buscar el recinto asociado a esta llave
            $recinto = Recinto::where('llave_id', $llave->id)->where('condicion', 1)->first();
            
            if (!$recinto) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un recinto asociado a esta llave'
                ], 404);
            }

            // Buscar bitácora activa para este recinto
            $bitacora = Bitacora::where('id_recinto', $recinto->id)
                              ->where('id_llave', $llave->id)
                              ->where('condicion', 1)
                              ->first();

            if (!$bitacora) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una bitácora activa para este recinto'
                ], 404);
            }

            Log::info('Estado actual de bitácora', [
                'bitacora_id' => $bitacora->id,
                'estado_bitacora' => $bitacora->estado,
                'estado_texto' => $bitacora->estado_texto
            ]);

            // Lógica de cambio de estado según el estado actual de la llave y bitácora
            if ($llave->estaDisponible() && $bitacora->estaPendiente()) {
                // Primera vez: entregar llave y activar bitácora
                $llave->marcarComoEntregada();
                $bitacora->activar();
                
                Log::info('Llave entregada y bitácora activada', [
                    'llave_id' => $llave->id,
                    'bitacora_id' => $bitacora->id,
                    'nuevo_estado_llave' => 'entregada',
                    'nuevo_estado_bitacora' => 'activa'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Llave entregada exitosamente. Bitácora activada.',
                    'accion' => 'entrega_llave',
                    'bitacora_id' => $bitacora->id,
                    'recinto' => $recinto->nombre,
                    'estado_llave' => 'entregada',
                    'estado_bitacora' => 'activa',
                    'estado_llave_texto' => $llave->estado_entrega_text,
                    'estado_bitacora_texto' => $bitacora->estado_texto,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
                
            } elseif ($llave->estaEntregada() && $bitacora->estaActiva()) {
                // Segunda vez: devolver llave y completar bitácora
                $llave->marcarComoDevuelta();
                $bitacora->completar();
                
                Log::info('Llave devuelta y bitácora completada', [
                    'llave_id' => $llave->id,
                    'bitacora_id' => $bitacora->id,
                    'nuevo_estado_llave' => 'disponible',
                    'nuevo_estado_bitacora' => 'completada'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Llave devuelta exitosamente. Bitácora completada.',
                    'accion' => 'devolucion_llave',
                    'bitacora_id' => $bitacora->id,
                    'recinto' => $recinto->nombre,
                    'estado_llave' => 'disponible',
                    'estado_bitacora' => 'completada',
                    'estado_llave_texto' => $llave->estado_entrega_text,
                    'estado_bitacora_texto' => $bitacora->estado_texto,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
                
            } elseif ($llave->estaDisponible() && $bitacora->estaCompletada()) {
                // Caso especial: reiniciar ciclo si es necesario
                $llave->marcarComoEntregada();
                $bitacora->activar();
                
                Log::info('Ciclo reiniciado - llave entregada y bitácora reactivada', [
                    'llave_id' => $llave->id,
                    'bitacora_id' => $bitacora->id
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ciclo reiniciado. Llave entregada y bitácora reactivada.',
                    'accion' => 'reinicio_ciclo',
                    'bitacora_id' => $bitacora->id,
                    'recinto' => $recinto->nombre,
                    'estado_llave' => 'entregada',
                    'estado_bitacora' => 'activa',
                    'estado_llave_texto' => $llave->estado_entrega_text,
                    'estado_bitacora_texto' => $bitacora->estado_texto,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Estado inconsistente. Llave: {$llave->estado_entrega_text}, Bitácora: {$bitacora->estado_texto}"
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en simulación de escaneo QR', [
                'error' => $e->getMessage(),
                'llave_id' => $request->input('llave_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la simulación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista de llaves para profesores
     */
    public function indexProfesor()
    {
        $user = Auth::user();
        $profesor = Profesor::where('usuario_id', $user->id)->first();
        
        if (!$profesor) {
            return redirect()->back()->with('error', 'No tienes perfil de profesor asignado.');
        }

        // Obtener solo los recintos asignados al profesor a través de sus horarios
        $recintos = $profesor->recintos()
                            ->with(['llave', 'tipoRecinto'])
                            ->where('recinto.condicion', 1)
                            ->get();

        return view('profesor.llaves.index', compact('recintos', 'profesor'));
    }

    /**
     * Vista administrativa de QR temporales
     */
    public function indexAdmin()
    {
        $llaves = Llave::with(['recinto' => function($query) {
            $query->with(['bitacoras' => function($subQuery) {
                $subQuery->where('condicion', 1)->orderBy('created_at', 'desc');
            }])->where('condicion', 1);
        }])->where('condicion', 1)->get();
        
        return view('qr.admin.index', compact('llaves'));
    }
}
