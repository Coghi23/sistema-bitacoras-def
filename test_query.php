<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Set up database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Testing database connections...\n";

// Test the corrected query
echo "\nUsers with horarios:\n";
$users = Capsule::table('users')
    ->join('horarios', 'users.id', '=', 'horarios.user_id')
    ->select('users.id', 'users.email')
    ->distinct()
    ->get();

foreach($users as $user) {
    echo "- User {$user->id}: {$user->email}\n";
}

// Test the main query for each user
echo "\nTesting main query for each user:\n";
foreach($users as $user) {
    echo "\nUser {$user->id} ({$user->email}):\n";
    $recintos = Capsule::table('horarios')
        ->join('recinto', 'horarios.idRecinto', '=', 'recinto.id')
        ->join('llave', 'recinto.llave_id', '=', 'llave.id')
        ->where('horarios.user_id', $user->id)
        ->select('recinto.id as recinto_id', 'recinto.nombre as recinto_nombre', 'llave.id as llave_id', 'llave.nombre as llave_nombre')
        ->distinct()
        ->get();
    
    if($recintos->count() > 0) {
        foreach($recintos as $recinto) {
            echo "  - Recinto: {$recinto->recinto_nombre} (ID: {$recinto->recinto_id})\n";
            echo "    Llave: {$recinto->llave_nombre} (ID: {$recinto->llave_id})\n";
        }
    } else {
        echo "  - No recintos found\n";
    }
}
