<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICACIÓN DE PERMISOS DEL SIDEBAR ===\n\n";

// Verificar usuario superadmin
$superadmin = App\Models\User::where('email', 'superadmin@sistema.com')->first();
if ($superadmin) {
    echo "👤 USUARIO SUPERADMIN:\n";
    echo "   Email: " . $superadmin->email . "\n";
    echo "   Roles: " . $superadmin->roles->pluck('name')->join(', ') . "\n";
    echo "   ¿Es superadmin?: " . ($superadmin->hasRole('superadmin') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "   Permisos del sidebar:\n";
    
    $sidebarPerms = [
        'view roles', 'view permisos', 'view usuarios', 'view instituciones', 
        'view especialidades', 'view secciones', 'view subareas', 'view llaves',
        'view tipo_recintos', 'view estado_recintos', 'view recintos', 'view horarios',
        'view qr_temporales', 'view bitacoras', 'view reportes'
    ];
    
    foreach ($sidebarPerms as $perm) {
        echo "     - " . $perm . ": " . ($superadmin->can($perm) ? '✅' : '❌') . "\n";
    }
    echo "\n";
} else {
    echo "❌ Usuario superadmin no encontrado\n\n";
}

// Verificar otros roles
$roles = ['profesor', 'soporte', 'director', 'administrador'];
foreach ($roles as $roleName) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();
    if ($role) {
        echo "🔒 ROL: " . strtoupper($roleName) . "\n";
        echo "   Permisos asignados:\n";
        foreach ($role->permissions as $perm) {
            echo "     - " . $perm->name . "\n";
        }
        echo "\n";
    }
}

echo "✅ Verificación completada\n";
