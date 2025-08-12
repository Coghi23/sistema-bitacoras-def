<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrTemporal;
use App\Models\Profesor;
use App\Models\Recinto;
use Carbon\Carbon;

class TestQrTemporal extends Command
{
    protected $signature = 'test:qr-temporal';
    protected $description = 'Probar funcionalidad QR temporal';

    public function handle()
    {
        $this->info('Probando QrTemporal...');
        
        try {
            // Intentar crear un QR temporal
            $profesor = Profesor::first();
            $recinto = Recinto::first();
            
            if (!$profesor || !$recinto) {
                $this->error('No hay profesor o recinto disponible');
                return 1;
            }
            
            $qr = QrTemporal::create([
                'recinto_id' => $recinto->id,
                'profesor_id' => $profesor->id,
                'llave_id' => $recinto->llave_id,
                'expira_en' => Carbon::now()->addMinutes(30)
            ]);
            
            $this->info("✅ QR creado: {$qr->codigo_qr}");
            
            // Probar consulta
            $qrs = QrTemporal::where('expira_en', '>', Carbon::now())->get();
            $this->info("📊 QRs activos: {$qrs->count()}");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
