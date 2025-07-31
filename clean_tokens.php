<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Limpiando tokens incorrectos ===\n";

$deleted = DB::table('password_reset_tokens')->delete();

echo "Tokens eliminados: {$deleted}\n";

echo "=== Verificando tabla ===\n";

$tokens = DB::table('password_reset_tokens')->get();
echo "Tokens restantes: " . $tokens->count() . "\n";
