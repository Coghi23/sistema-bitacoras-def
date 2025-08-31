<?php
require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Horario;

try {
    $horarios = Horario::with(['recinto', 'seccion', 'subarea', 'leccion'])->get();
    echo "Horarios encontrados: " . $horarios->count() . "\n";
    
    if ($horarios->count() > 0) {
        $primer_horario = $horarios->first();
        echo "Primer horario ID: " . $primer_horario->id . "\n";
        echo "Recinto: " . (optional($primer_horario->recinto)->nombre ?? 'Sin recinto') . "\n";
        echo "Sección: " . (optional($primer_horario->seccion)->nombre ?? 'Sin sección') . "\n";
        echo "Subárea: " . (optional($primer_horario->subarea)->nombre ?? 'Sin subárea') . "\n";
        
        $primera_leccion = $primer_horario->leccion->first();
        if ($primera_leccion) {
            echo "Primera lección: " . $primera_leccion->leccion . "\n";
        } else {
            echo "Sin lecciones\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
