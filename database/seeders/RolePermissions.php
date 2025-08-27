<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles básicos
        $roles = [
            'administrador',
            'profesor',
            'soporte',
            'director'
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }

        // Crear permisos específicos para el director (solo lectura)
        $permissions = [
            'view_usuarios',
            'view_bitacoras',
            'view_eventos',
            'view_reportes',
            'view_instituciones',
            'view_profesores',
            'view_secciones',
            'view_dashboard'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos de solo vista al director
        $director = \Spatie\Permission\Models\Role::where('name', 'director')->first();
        if ($director) {
            $director->syncPermissions($permissions);
        }

        // El administrador tiene todos los permisos
        $admin = \Spatie\Permission\Models\Role::where('name', 'administrador')->first();
        if ($admin) {
            // Crear permisos completos para administrador
            $adminPermissions = [
                'create_usuarios', 'edit_usuarios', 'delete_usuarios', 'view_usuarios',
                'create_bitacoras', 'edit_bitacoras', 'delete_bitacoras', 'view_bitacoras',
                'create_eventos', 'edit_eventos', 'delete_eventos', 'view_eventos',
                'create_reportes', 'edit_reportes', 'delete_reportes', 'view_reportes',
                'create_instituciones', 'edit_instituciones', 'delete_instituciones', 'view_instituciones',
                'create_profesores', 'edit_profesores', 'delete_profesores', 'view_profesores',
                'create_secciones', 'edit_secciones', 'delete_secciones', 'view_secciones',
                'view_dashboard'
            ];

            foreach ($adminPermissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            }

            $admin->syncPermissions($adminPermissions);
        }
    }
}