<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Models\User;

echo "=== Probando configuración de email ===\n\n";

try {
    // Verificar configuración
    echo "Configuración actual:\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
    echo "MAIL_FROM: " . config('mail.from.address') . "\n\n";
    
    // Buscar un usuario para probar
    $user = User::first();
    
    if (!$user) {
        echo "❌ No hay usuarios en la base de datos.\n";
        echo "Crea un usuario primero.\n";
        exit;
    }
    
    echo "Usuario encontrado: {$user->email}\n";
    echo "¿Quieres enviar un email de prueba? (s/n): ";
    
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) === 's') {
        echo "\nEnviando email de reset de contraseña...\n";
        
        $status = Password::sendResetLink(['email' => $user->email]);
        
        if ($status === Password::RESET_LINK_SENT) {
            echo "✅ Email enviado exitosamente!\n";
            echo "Revisa tu bandeja de entrada.\n";
        } else {
            echo "❌ Error enviando email: $status\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Verifica tu configuración de email en .env\n";
}

echo "\n=== Fin de la prueba ===\n";
