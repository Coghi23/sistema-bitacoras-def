<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICACIÓN DE PERMISOS ACTUALIZADOS ===\n\n";

// Verificar que los permisos existen
$qrPermisos = ['create_qr_temporales', 'view_qr_temporales', 'edit_qr_temporales', 'delete_qr_temporales'];
$bitacoraPermisos = ['create_bitacoras', 'view_bitacoras', 'edit_bitacoras', 'delete_bitacoras'];

echo "📋 PERMISOS CREADOS:\n";
foreach (array_merge($qrPermisos, $bitacoraPermisos) as $permiso) {
    $existe = Spatie\Permission\Models\Permission::where('name', $permiso)->exists();
    echo "   - $permiso: " . ($existe ? '✅' : '❌') . "\n";
}

echo "\n";

// Verificar usuario superadmin
$superadmin = App\Models\User::where('email', 'superadmin@sistema.com')->first();
if ($superadmin) {
    echo "👤 USUARIO SUPERADMIN:\n";
    echo "   ¿Puede ver QR temporales?: " . ($superadmin->can('view_qr_temporales') ? '✅' : '❌') . "\n";
    echo "   ¿Puede ver bitácoras?: " . ($superadmin->can('view_bitacoras') ? '✅' : '❌') . "\n";
    echo "\n";
}

// Verificar roles
echo "🔒 ROLES Y SUS PERMISOS DE QR/BITÁCORA:\n";
$roles = ['profesor', 'soporte', 'director', 'administrador'];
foreach ($roles as $roleName) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();
    if ($role) {
        echo "   $roleName:\n";
        echo "     - view_qr_temporales: " . ($role->hasPermissionTo('view_qr_temporales') ? '✅' : '❌') . "\n";
        echo "     - view_bitacoras: " . ($role->hasPermissionTo('view_bitacoras') ? '✅' : '❌') . "\n";
    }
}

echo "\n✅ Verificación completada\n";
