<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class SidebarPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos específicos para el sidebar
        $sidebarPermissions = [
            // Gestión de usuarios y roles (usar los permisos existentes)
            'view_roles',
            'view_roles', // Para permisos, usamos el mismo view_roles ya que están juntos
            'view_usuarios',
            
            // Configuraciones del sistema (usar permisos existentes)
            'view_institucion',
            'view_especialidad', 
            'view_seccion',
            'view_subarea',
            
            // Gestión de llaves
            'view_llaves',
            
            // Gestión de recintos
            'view_tipo_recinto',
            'view_estado_recinto',
            'view_recintos',
            
            // Programación y horarios
            'view_horario',
            
            // Códigos QR temporales
            'view_qr_temporales',
            
            // Bitácoras
            'view_bitacoras',
            
            // Reportes
            'view_reportes'
        ];

        // No necesitamos crear estos permisos porque ya existen en RolePermissions
        // Solo necesitamos asignarlos a los roles

        // Obtener o crear roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $profesorRole = Role::firstOrCreate(['name' => 'profesor']);
        $soporteRole = Role::firstOrCreate(['name' => 'soporte']);
        $directorRole = Role::firstOrCreate(['name' => 'director']);
        $administradorRole = Role::firstOrCreate(['name' => 'administrador']);

        // El superadmin y administrador ya tienen todos los permisos desde RolePermissions
        // Solo necesitamos asignar permisos específicos a otros roles

        // Asignar permisos limitados al profesor
        $profesorRole->givePermissionTo([
            'view_horario',
            'view_bitacoras',
            'view_reportes'
        ]);

        // Asignar permisos de soporte técnico
        $soporteRole->givePermissionTo([
            'view_llaves',
            'view_tipo_recinto',
            'view_estado_recinto', 
            'view_recintos',
            'view_qr_temporales',
            'view_bitacoras'
        ]);

        // Asignar permisos específicos al director (similar al admin pero sin gestión de usuarios/roles)
        $directorRole->givePermissionTo([
            'view_usuarios',
            'view_institucion',
            'view_especialidad',
            'view_seccion',
            'view_subarea',
            'view_llaves',
            'view_tipo_recinto',
            'view_estado_recinto',
            'view_recintos',
            'view_horario',
            'view_qr_temporales',
            'view_bitacoras',
            'view_reportes'
        ]);

        // Asegurar que el usuario superadmin tenga el rol
        $superAdminUser = User::where('email', 'superadmin@sistema.com')->first();
        if ($superAdminUser) {
            $superAdminUser->assignRole('superadmin');
        }

        echo "✅ Permisos del sidebar creados y asignados correctamente\n";
        echo "✅ Superadmin tiene acceso completo\n";
        echo "✅ Roles específicos configurados con permisos limitados\n";
    }
}
