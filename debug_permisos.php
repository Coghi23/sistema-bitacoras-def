<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PERMISOS DISPONIBLES EN EL SISTEMA ===\n";
foreach(Spatie\Permission\Models\Permission::all() as $p) {
    echo "- " . $p->name . "\n";
}

echo "\n=== USUARIOS SUPERADMIN ===\n";
$superadmins = App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'superadmin');
})->get();

foreach($superadmins as $user) {
    echo "Usuario: " . $user->name . "\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Permisos directos: " . $user->permissions->pluck('name')->join(', ') . "\n";
    echo "Todos los permisos (incluye roles): \n";
    foreach($user->getAllPermissions() as $permiso) {
        echo "  - " . $permiso->name . "\n";
    }
    echo "\n";
}
