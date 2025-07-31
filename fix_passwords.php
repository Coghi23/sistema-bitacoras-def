<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Verificando contraseñas de usuarios ===\n";

$users = User::all();

if ($users->count() == 0) {
    echo "No hay usuarios en la base de datos.\n";
    exit;
}

foreach ($users as $user) {
    echo "ID: {$user->id} | Email: {$user->email}\n";
    echo "  Password actual: " . substr($user->password, 0, 20) . "...\n";
    echo "  Longitud: " . strlen($user->password) . "\n";
    echo "  Es Bcrypt: " . (password_get_info($user->password)['algo'] !== null ? 'SÍ' : 'NO') . "\n";
    
    // Si no es bcrypt, la convertimos
    if (password_get_info($user->password)['algo'] === null) {
        echo "  🔧 CONVIRTIENDO a Bcrypt...\n";
        
        // Asumimos que la contraseña actual es el texto plano
        $newPassword = Hash::make($user->password);
        
        User::where('id', $user->id)->update(['password' => $newPassword]);
        
        echo "  ✅ Contraseña actualizada\n";
    } else {
        echo "  ✅ Ya está en formato correcto\n";
    }
    
    echo "---\n";
}

echo "=== PROCESO COMPLETADO ===\n";
