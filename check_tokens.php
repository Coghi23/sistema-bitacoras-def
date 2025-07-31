<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Tokens de Password Reset ===\n";

$tokens = DB::table('password_reset_tokens')->get();

foreach ($tokens as $token) {
    echo "Email: {$token->email}\n";
    echo "Token: {$token->token}\n";
    echo "Created: {$token->created_at}\n";
    echo "Token Length: " . strlen($token->token) . "\n";
    echo "---\n";
}

echo "Total tokens: " . $tokens->count() . "\n";
