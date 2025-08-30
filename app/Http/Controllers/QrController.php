<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrTemporal;
use App\Models\Recinto;
use App\Models\Profesor;
use App\Models\Llave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
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
     * Generar QR temporal para un recinto específico
     */
    public function generarQr(Request $request)
    {
        $request->validate([
            'recinto_id' => 'required|exists:recinto,id'
        ]);

        $user = Auth::user();
        $profesor = Profesor::where('usuario_id', $user->id)->first();
        
        if (!$profesor) {
            return response()->json(['error' => 'No tienes perfil de profesor asignado.'], 403);
        }

        $recinto = Recinto::with('llave')->find($request->recinto_id);
        
        if (!$recinto || !$recinto->llave) {
            return response()->json(['error' => 'Recinto o llave no encontrados.'], 404);
        }

        // Generar código QR simple
        $qrCode = 'QR-' . $recinto->id . '-' . $profesor->id . '-' . time();
        
        // Guardar en base de datos usando SQL directo para evitar problemas de modelo
        \DB::table('qr_temporales')->insert([
            'codigo_qr' => $qrCode,
            'recinto_id' => $recinto->id,
            'profesor_id' => $profesor->id,
            'llave_id' => $recinto->llave->id,
            'usado' => false,
            'expira_en' => Carbon::now()->addMinutes(30),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCode),
            'expira_en' => Carbon::now()->addMinutes(30)->format('H:i:s'),
            'mensaje' => 'QR generado exitosamente'
        ]);
    }

    /**
     * Vista administrativa de QR temporales
     */
    public function indexAdmin()
    {
        // Usar SQL directo para evitar problemas con el modelo
        $qrsTemporales = \DB::table('qr_temporales')
            ->join('recinto', 'qr_temporales.recinto_id', '=', 'recinto.id')
            ->join('profesor', 'qr_temporales.profesor_id', '=', 'profesor.id')
            ->join('users', 'profesor.usuario_id', '=', 'users.id')
            ->join('llave', 'qr_temporales.llave_id', '=', 'llave.id')
            ->where('qr_temporales.expira_en', '>', Carbon::now())
            ->select(
                'qr_temporales.*',
                'recinto.nombre as recinto_nombre',
                'users.name as profesor_nombre',
                'llave.nombre as llave_nombre',
                'llave.estado as llave_estado'
            )
            ->orderBy('qr_temporales.created_at', 'desc')
            ->get();

        return view('admin.qr.index', compact('qrsTemporales'));
    }

    /**
     * Escanear QR y cambiar estado de llave
     */
    public function escanearQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        // Buscar QR usando SQL directo
        $qrTemporal = \DB::table('qr_temporales')
            ->join('llave', 'qr_temporales.llave_id', '=', 'llave.id')
            ->join('recinto', 'qr_temporales.recinto_id', '=', 'recinto.id')
            ->join('profesor', 'qr_temporales.profesor_id', '=', 'profesor.id')
            ->join('users', 'profesor.usuario_id', '=', 'users.id')
            ->where('qr_temporales.codigo_qr', $request->qr_code)
            ->select(
                'qr_temporales.*',
                'llave.id as llave_id',
                'llave.nombre as llave_nombre',
                'llave.estado as llave_estado',
                'recinto.id as recinto_id',
                'recinto.nombre as recinto_nombre',
                'users.name as profesor_nombre'
            )
            ->first();

        if (!$qrTemporal) {
            return response()->json(['error' => 'Código QR no válido.'], 404);
        }

        if ($qrTemporal->usado || $qrTemporal->expira_en < \Carbon\Carbon::now()) {
            return response()->json(['error' => 'Código QR expirado o ya usado.'], 400);
        }

        // Cambiar estado de la llave
        $nuevoEstado = $qrTemporal->llave_estado == 0 ? 1 : 0;
        \DB::table('llave')->where('id', $qrTemporal->llave_id)->update(['estado' => $nuevoEstado]);

        // Buscar bitácora activa para esta llave y recinto
        $bitacora = \DB::table('bitacora')
            ->where('id_llave', $qrTemporal->llave_id)
            ->where('id_recinto', $qrTemporal->recinto_id)
            ->where('condicion', 1)
            ->orderByDesc('id')
            ->first();

        $bitacoraInfo = null;
        if ($bitacora) {
            // El estado de la bitácora debe coincidir con el estado de la llave
            \DB::table('bitacora')->where('id', $bitacora->id)->update(['estado' => $nuevoEstado]);
            $bitacoraInfo = [
                'id' => $bitacora->id,
                'estado' => $nuevoEstado == 1 ? 'Activo' : 'Inactivo'
            ];
        } else {
            // Crear nueva entrada en bitácora si no existe
            $bitacoraId = \DB::table('bitacora')->insertGetId([
                'id_recinto' => $qrTemporal->recinto_id,
                'id_llave' => $qrTemporal->llave_id,
                'estado' => $nuevoEstado,
                'condicion' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $bitacoraInfo = [
                'id' => $bitacoraId,
                'estado' => $nuevoEstado == 1 ? 'Activo' : 'Inactivo'
            ];
        }

        // Marcar QR como usado
        \DB::table('qr_temporales')->where('id', $qrTemporal->id)->update(['usado' => true]);

        $accion = $nuevoEstado == 1 ? 'entregada' : 'devuelta';
        $mensaje = "Llave '{$qrTemporal->llave_nombre}' {$accion} por {$qrTemporal->profesor_nombre}";

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje,
            'llave' => [
                'nombre' => $qrTemporal->llave_nombre,
                'estado' => $nuevoEstado == 0 ? 'No Entregada' : 'Entregada'
            ],
            'bitacora' => $bitacoraInfo
        ]);
    }
}
