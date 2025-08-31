<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateLlaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreLlaveRequest;
use App\Models\Llave;
use Carbon\Carbon;
use Exception;
use PgSql\Lob;
use Illuminate\Http\Request;

class LlaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Llave::with('recinto');

        // Aplicar filtro de bÃºsqueda si existe
        if ($request->filled('busquedaLlave')) {
            $busqueda = $request->get('busquedaLlave');
            $query->where('nombre', 'LIKE', '%' . $busqueda . '%');
        }

        $llaves = $query->get();
        
        return view('Llave.index', compact('llaves'));
    }

    /**
     * Obtener estado de llaves en tiempo real (AJAX)
     */
    public function getLlavesRealTime()
    {
        try {
            $llaves = Llave::where('condicion', 1)
                ->select('id', 'nombre', 'estado', 'updated_at')
                ->get()
                ->map(function($llave) {
                    return [
                        'id' => $llave->id,
                        'nombre' => $llave->nombre,
                        'estado' => $llave->estado,
                        'estado_texto' => $llave->estado == 0 ? 'Entregada' : 'No Entregada',
                        'estado_badge_class' => $llave->estado == 0 ? 'bg-success' : 'bg-warning text-dark',
                        'ultima_actualizacion' => $llave->updated_at->format('d/m/Y H:i:s')
                    ];
                });

            return response()->json([
                'status' => 'success',
                'llaves' => $llaves,
                'total' => $llaves->count(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener llaves: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener QRs temporales activos en tiempo real (AJAX)
     */
    public function getQRsTemporalesRealTime()
    {
        try {
            $qrsTemporales = DB::table('qr_temporales')
                ->join('profesor', 'qr_temporales.profesor_id', '=', 'profesor.id')
                ->join('users', 'profesor.usuario_id', '=', 'users.id')
                ->join('recinto', 'qr_temporales.recinto_id', '=', 'recinto.id')
                ->join('llave', 'qr_temporales.llave_id', '=', 'llave.id')
                ->where('qr_temporales.expira_en', '>', Carbon::now())
                ->where('qr_temporales.usado', false) // Excluir QRs ya escaneados
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
                        'profesor_nombre' => $qr->profesor_nombre,
                        'recinto_nombre' => $qr->recinto_nombre,
                        'llave_nombre' => $qr->llave_nombre,
                        'llave_estado' => $qr->llave_estado,
                        'llave_estado_texto' => $qr->llave_estado == 0 ? 'Entregada' : 'No Entregada',
                        'llave_estado_badge' => $qr->llave_estado == 0 ? 'bg-success' : 'bg-warning text-dark',
                        'usado' => $qr->usado,
                        'estado_qr' => $qr->usado ? 'Usado' : 'Activo',
                        'estado_qr_badge' => $qr->usado ? 'bg-secondary' : 'bg-success',
                        'created_at' => Carbon::parse($qr->created_at)->format('d/m/Y H:i:s'),
                        'expira_en' => $expiraEn->format('d/m/Y H:i:s'),
                        'expira_class' => $expiraEn < $ahora ? 'text-danger' : 'text-warning',
                        'tiempo_restante' => $expiraEn->diffInMinutes($ahora) . ' min',
                        'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$qr->codigo_qr}"
                    ];
                });

            return response()->json([
                'status' => 'success',
                'qrs' => $qrsTemporales,
                'total_activos' => $qrsTemporales->where('usado', false)->count(),
                'total_usados' => $qrsTemporales->where('usado', true)->count(),
                'total' => $qrsTemporales->count(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener QRs temporales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Llave.create');
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
        return view('Llave.index', compact('llaves', 'llave'));
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