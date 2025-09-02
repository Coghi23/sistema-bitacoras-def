<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Crear usuario de prueba con rol soporte
$testUser = App\Models\User::firstOrCreate(
    ['email' => 'soporte@test.com'],
    [
        'name' => 'Usuario Soporte Test',
        'cedula' => '12345678',
        'password' => bcrypt('password123'),
        'condicion' => true,
    ]
);

// Asignar rol de soporte
$testUser->assignRole('soporte');

echo "✅ Usuario de prueba creado:\n";
echo "   Email: soporte@test.com\n";
echo "   Password: password123\n";
echo "   Rol: soporte\n\n";

// Verificar permisos
echo "📋 PERMISOS DEL USUARIO SOPORTE:\n";
$sidebarPerms = [
    'view_roles', 'view_usuarios', 'view_institucion', 'view_especialidad', 
    'view_seccion', 'view_subarea', 'view_llaves', 'view_tipo_recinto', 
    'view_estado_recinto', 'view_recintos', 'view_horario', 'view_qr_temporales', 
    'view_bitacoras', 'view_reportes'
];

foreach ($sidebarPerms as $perm) {
    echo "   - " . $perm . ": " . ($testUser->can($perm) ? '✅' : '❌') . "\n";
}

echo "\n✅ Usuario de prueba configurado correctamente\n";
