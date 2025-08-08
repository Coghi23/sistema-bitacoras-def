<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Verificar roles existentes
echo "Roles actuales:\n";
$roles = Role::all();
foreach($roles as $role) {
    echo "- " . $role->name . "\n";
}

// Crear rol director si no existe
if (!Role::where('name', 'director')->exists()) {
    $director = Role::create(['name' => 'director']);
    echo "\nRol 'director' creado exitosamente.\n";
} else {
    echo "\nRol 'director' ya existe.\n";
}

// Crear permisos de solo lectura si no existen
$permissions = [
    'view_usuarios',
    'view_bitacoras',
    'view_eventos',
    'view_reportes',
    'view_instituciones',
    'view_profesores',
    'view_secciones'
];

foreach($permissions as $permission) {
    if (!Permission::where('name', $permission)->exists()) {
        Permission::create(['name' => $permission]);
        echo "Permiso '$permission' creado.\n";
    }
}

// Asignar permisos de solo vista al director
$director = Role::where('name', 'director')->first();
if($director) {
    $director->syncPermissions($permissions);
    echo "\nPermisos de solo vista asignados al rol 'director'.\n";
}

echo "\nConfiguración completada.\n";
