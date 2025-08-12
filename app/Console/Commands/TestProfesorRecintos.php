<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\Recinto;

class TestProfesorRecintos extends Command
{
    protected $signature = 'test:profesor-recintos';
    protected $description = 'Probar relaciones profesor-recintos';

    public function handle()
    {
        $this->info('Probando relaciones profesor-recintos...');
        
        // Verificar profesores
        $profesores = Profesor::count();
        $this->info("Total profesores: {$profesores}");
        
        // Verificar horarios
        $horarios = Horario::count();
        $this->info("Total horarios: {$horarios}");
        
        // Verificar recintos
        $recintos = Recinto::count();
        $this->info("Total recintos: {$recintos}");
        
        if ($profesores > 0) {
            $profesor = Profesor::first();
            $this->info("Primer profesor: {$profesor->usuario->name}");
            
            try {
                $recintosProfesor = $profesor->recintos()->count();
                $this->info("Recintos del profesor: {$recintosProfesor}");
            } catch (\Exception $e) {
                $this->error("Error en relación recintos: " . $e->getMessage());
            }
        }
        
        return 0;
    }
}
