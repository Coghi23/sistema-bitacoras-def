<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "=== Creando token de prueba ===\n";

// Limpiar tokens existentes
DB::table('password_reset_tokens')->delete();

// Generar token plano
$plainToken = Str::random(64);
$hashedToken = Hash::make($plainToken);

// Insertar en la base de datos
DB::table('password_reset_tokens')->insert([
    'email' => 'gabrielpeca29@gmail.com',
    'token' => $hashedToken,
    'created_at' => now()
]);

echo "Token plano: {$plainToken}\n";
echo "Token hasheado: {$hashedToken}\n";
echo "URL para probar: \n";
echo "http://sistema-bitacoras-def.test/reset-password/{$plainToken}?email=gabrielpeca29@gmail.com\n";

echo "\n=== Token creado exitosamente ===\n";
