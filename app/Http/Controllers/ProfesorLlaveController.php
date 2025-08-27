<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recinto;
use App\Models\Profesor;
use App\Models\Llave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfesorLlaveController extends Controller
{
    /**
     * Vista principal de llaves para profesores
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            $profesor = Profesor::where('usuario_id', $user->id)->first();
            
            // Auto-crear perfil si el usuario tiene rol profesor pero no tiene perfil
            if (!$profesor && $user->hasRole('profesor')) {
                $profesor = Profesor::create(['usuario_id' => $user->id]);
                \Log::info("Perfil de profesor auto-creado para usuario: {$user->name}");
            }
            
            if (!$profesor) {
                return view('profesor-llave.index', [
                    'error' => 'No tienes perfil de profesor asignado. Contacta al administrador.',
                    'profesor' => null,
                    'recintos' => collect(),
                    'qrsTemporales' => collect()
                ]);
            }

            // Obtener recintos asignados al profesor usando SQL directo
            $recintos = DB::table('horarios')
                ->join('recinto', 'horarios.idRecinto', '=', 'recinto.id')
                ->join('llave', 'recinto.llave_id', '=', 'llave.id')
                ->where('horarios.user_id', $user->id)
                ->where('recinto.condicion', 1)
                ->select(
                    'recinto.id as recinto_id',
                    'recinto.nombre as recinto_nombre',
                    'llave.id as llave_id',
                    'llave.nombre as llave_nombre',
                    'llave.estado as llave_estado'
                )
                ->distinct()
                ->get();

            // Obtener QRs temporales activos usando SQL directo
            $qrsTemporales = DB::table('qr_temporales')
                ->join('profesor', 'qr_temporales.profesor_id', '=', 'profesor.id')
                ->join('users', 'profesor.usuario_id', '=', 'users.id')
                ->join('recinto', 'qr_temporales.recinto_id', '=', 'recinto.id')
                ->join('llave', 'qr_temporales.llave_id', '=', 'llave.id')
                ->where('profesor.usuario_id', $user->id)
                ->where('qr_temporales.expira_en', '>', now())
                ->select(
                    'qr_temporales.*',
                    'users.name as profesor_nombre',
                    'recinto.nombre as recinto_nombre',
                    'llave.nombre as llave_nombre',
                    'llave.estado as llave_estado'
                )
                ->orderBy('qr_temporales.created_at', 'desc')
                ->get();

            return view('profesor-llave.index', compact('profesor', 'recintos', 'qrsTemporales'));
            
        } catch (\Exception $e) {
            return view('profesor-llave.index', [
                'error' => 'Error del sistema: ' . $e->getMessage(),
                'profesor' => null,
                'recintos' => collect(),
                'qrsTemporales' => collect()
            ]);
        }
    }
    
    /**
     * Mostrar vista del escáner QR
     */
    public function scanner()
    {
        return view('profesor-llave.scanner');
    }
    
    /**
     * Generar código QR temporal
     */
    public function generarQr(Request $request)
    {
        $request->validate([
            'recinto_id' => 'required|exists:recinto,id',
            'llave_id' => 'required|exists:llave,id'
        ]);

        $user = Auth::user();
        $profesor = Profesor::where('usuario_id', $user->id)->first();

        if (!$profesor) {
            return response()->json(['success' => false, 'message' => 'No tienes perfil de profesor']);
        }

        // Generar código único
        $codigoQr = 'QR-' . strtoupper(uniqid());
        $expiraEn = Carbon::now()->addMinutes(30);

        // Insertar usando SQL directo
        DB::table('qr_temporales')->insert([
            'codigo_qr' => $codigoQr,
            'profesor_id' => $profesor->id,
            'recinto_id' => $request->recinto_id,
            'llave_id' => $request->llave_id,
            'expira_en' => $expiraEn,
            'usado' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'codigo_qr' => $codigoQr,
            'expira_en' => $expiraEn->format('Y-m-d H:i:s'),
            'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$codigoQr}"
        ]);
    }

    /**
     * Escanear código QR
     */
    public function escanearQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        // Buscar QR usando SQL directo
        $qr = DB::table('qr_temporales')
            ->where('codigo_qr', $request->qr_code)
            ->where('usado', false)
            ->where('expira_en', '>', now())
            ->first();

        if (!$qr) {
            return response()->json(['success' => false, 'error' => 'Código QR inválido o expirado']);
        }

        // Obtener estado actual de la llave
        $llave = DB::table('llave')->where('id', $qr->llave_id)->first();
        
        if (!$llave) {
            return response()->json(['success' => false, 'error' => 'Llave no encontrada']);
        }

        // Cambiar estado de la llave
        $nuevoEstado = $llave->estado == 0 ? 1 : 0;
        
        DB::table('llave')
            ->where('id', $qr->llave_id)
            ->update(['estado' => $nuevoEstado]);

        // Marcar QR como usado
        DB::table('qr_temporales')
            ->where('id', $qr->id)
            ->update(['usado' => true, 'updated_at' => now()]);

        $mensaje = $nuevoEstado == 1 ? 
            "Llave {$llave->nombre} entregada correctamente" : 
            "Llave {$llave->nombre} devuelta correctamente";

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje,
            'nuevo_estado' => $nuevoEstado
        ]);
    }
}
