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
            'superadmin',
            'administrador',
            'profesor',
            'soporte',
            'director'
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }


        // Permisos CRUD por vista
        $permissions = [
            // Evento
            'create_eventos', 'view_eventos', 'edit_eventos', 'delete_eventos',
            // Usuario
            'create_usuarios', 'view_usuarios', 'edit_usuarios', 'delete_usuarios',
            // Especialidad
            'create_especialidad', 'view_especialidad', 'edit_especialidad', 'delete_especialidad',
            // Institucion
            'create_institucion', 'view_institucion', 'edit_institucion', 'delete_institucion', 'restore_institucion',
            // Recinto
            'create_recintos', 'view_recintos', 'edit_recintos', 'delete_recintos',
            // Seccion
            'view_seccion', 'edit_seccion', 'delete_seccion',
            // Subarea
            'create_subarea', 'view_subarea', 'delete_subarea',
            // Llave
            'create_llaves', 'view_llaves', 'edit_llaves', 'delete_llaves',
            // EstadoRecinto
            'create_estado_recinto', 'view_estado_recinto', 'edit_estado_recinto', 'delete_estado_recinto',
            // Horario
            'create_horario', 'view_horario', 'edit_horario', 'delete_horario',
            // TipoRecinto
            'create_tipo_recinto', 'view_tipo_recinto', 'edit_tipo_recinto', 'delete_tipo_recinto',
            // Role
            'create_roles', 'view_roles', 'edit_roles', 'delete_roles',
            // QR Temporales
            'create_qr_temporales', 'view_qr_temporales', 'edit_qr_temporales', 'delete_qr_temporales',
            // Bitácora
            'create_bitacoras', 'view_bitacoras', 'edit_bitacoras', 'delete_bitacoras',
            // Reportes
            'view_reportes', 'export_reportes',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // El administrador tiene todos los permisos
        $admin = \Spatie\Permission\Models\Role::where('name', 'administrador')->first();
        if ($admin) {
            // Asignar todos los permisos existentes al administrador
            $admin->syncPermissions(Permission::all());
        }

        $superadmin = \Spatie\Permission\Models\Role::where('name', 'superadmin')->first();
        if($superadmin) {
            $superadmin->syncPermissions(Permission::all());
        };
    }
}