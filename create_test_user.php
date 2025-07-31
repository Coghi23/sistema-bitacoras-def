<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Creando usuario de prueba ===\n";

// Cambiar este email por tu email real
$email = 'tu-email-real@gmail.com';
$password = 'password123';

// Verificar si el usuario ya existe
$user = User::where('email', $email)->first();

if ($user) {
    echo "Usuario ya existe: {$email}\n";
} else {
    User::create([
        'name' => 'Usuario Prueba',
        'email' => $email,
        'password' => $password, // Se hashea automáticamente por el cast
        'cedula' => '12345678',
        'condicion' => 'activo',
    ]);
    
    echo "Usuario creado: {$email}\n";
    echo "Contraseña: {$password}\n";
}

echo "\n=== ¡Listo para probar reset de contraseña! ===\n";
echo "1. Ve a: http://sistema-bitacoras-def.test/forgot-password\n";
echo "2. Introduce: {$email}\n";
echo "3. Revisa tu email real para el enlace\n";
