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
                ->where('qr_temporales.usado', false) // Excluir QRs ya escaneados
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

        \Log::info("🔍 Escaneando QR: " . $request->qr_code);

        // Buscar QR usando SQL directo
        $qr = DB::table('qr_temporales')
            ->where('codigo_qr', $request->qr_code)
            ->where('usado', false)
            ->where('expira_en', '>', now())
            ->first();

        if (!$qr) {
            \Log::warning("❌ QR no encontrado o ya usado: " . $request->qr_code);
            return response()->json(['success' => false, 'error' => 'Código QR inválido o expirado']);
        }

        \Log::info("✅ QR encontrado, ID: " . $qr->id);

        // Obtener estado actual de la llave
        $llave = DB::table('llave')->where('id', $qr->llave_id)->first();
        
        if (!$llave) {
            \Log::error("❌ Llave no encontrada, ID: " . $qr->llave_id);
            return response()->json(['success' => false, 'error' => 'Llave no encontrada']);
        }

        // Cambiar estado de la llave
        $nuevoEstado = $llave->estado == 0 ? 1 : 0;
        
        $llaveUpdated = DB::table('llave')
            ->where('id', $qr->llave_id)
            ->update(['estado' => $nuevoEstado]);

        // Marcar QR como usado
        $qrUpdated = DB::table('qr_temporales')
            ->where('id', $qr->id)
            ->update(['usado' => true, 'updated_at' => now()]);

        \Log::info("🔄 QR marcado como usado - Llave updated: $llaveUpdated, QR updated: $qrUpdated");

        $mensaje = $nuevoEstado == 1 ? 
            "Llave {$llave->nombre} entregada correctamente" : 
            "Llave {$llave->nombre} devuelta correctamente";

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje,
            'nuevo_estado' => $nuevoEstado,
            'qr_id' => $qr->id,
            'debug' => [
                'qr_updated' => $qrUpdated,
                'llave_updated' => $llaveUpdated
            ]
        ]);
    }
    
    /**
     * Obtener QRs temporales del profesor en tiempo real (AJAX)
     */
    public function getQRsRealTime()
    {
        try {
            $user = Auth::user();
            $profesor = Profesor::where('usuario_id', $user->id)->first();

            if (!$profesor) {
                return response()->json(['success' => false, 'message' => 'No tienes perfil de profesor']);
            }

            \Log::info("🔄 Consultando QRs tiempo real para profesor ID: " . $profesor->id);

            // Obtener QRs temporales activos (no usados y no expirados)
            $qrsTemporales = DB::table('qr_temporales')
                ->join('profesor', 'qr_temporales.profesor_id', '=', 'profesor.id')
                ->join('users', 'profesor.usuario_id', '=', 'users.id')
                ->join('recinto', 'qr_temporales.recinto_id', '=', 'recinto.id')
                ->join('llave', 'qr_temporales.llave_id', '=', 'llave.id')
                ->where('profesor.usuario_id', $user->id)
                ->where('qr_temporales.expira_en', '>', Carbon::now())
                ->where('qr_temporales.usado', false)
                ->select(
                    'qr_temporales.*',
                    'users.name as profesor_nombre',
                    'recinto.nombre as recinto_nombre',
                    'llave.nombre as llave_nombre',
                    'llave.estado as llave_estado'
                )
                ->orderBy('qr_temporales.created_at', 'desc')
                ->get()
                ->map(function($qr) {
                    $expiraEn = Carbon::parse($qr->expira_en);
                    $ahora = Carbon::now();
                    
                    return [
                        'id' => $qr->id,
                        'codigo_qr' => $qr->codigo_qr,
                        'usado' => $qr->usado, // Debug
                        'recinto_nombre' => $qr->recinto_nombre,
                        'llave_nombre' => $qr->llave_nombre,
                        'llave_estado' => $qr->llave_estado,
                        'expira_en' => $expiraEn->format('Y-m-d H:i:s'),
                        'expira_en_humano' => $expiraEn->diffForHumans($ahora),
                        'tiempo_restante_minutos' => max(0, $ahora->diffInMinutes($expiraEn, false))
                    ];
                });

            \Log::info("📊 QRs encontrados: " . $qrsTemporales->count());

            return response()->json([
                'status' => 'success',
                'qrs' => $qrsTemporales,
                'total' => $qrsTemporales->count(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'debug' => [
                    'usuario_id' => $user->id,
                    'profesor_id' => $profesor->id
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ Error en getQRsRealTime: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener QRs: ' . $e->getMessage()
            ], 500);
        }
    }
}
