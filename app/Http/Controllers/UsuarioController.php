<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Institucione;
use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inactivos = $request->query('inactivos');
        $query = User::with('roles');
        
        if ($inactivos) {
            $query->where('condicion', false);
        } else {
            $query->where('condicion', true);
        }

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('busquedaUsuario')) {
            $busqueda = $request->get('busquedaUsuario');
            $query->where(function($q) use ($busqueda) {
                $q->where('name', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('email', 'LIKE', '%' . $busqueda . '%');
            });
        }

        $usuarios = $query->get();
        $roles = Role::all();
        
        return view('Usuario.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('Usuario.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cedula' => 'required|unique:users,cedula',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
        ], [
            'cedula.unique' => 'La cédula ya está registrada.',
            'email.unique' => 'El correo ya está registrado.',
        ]);
        
        try {
            DB::beginTransaction();

            // Crear el usuario con una contraseña temporal (será reemplazada por el reset)
            $usuario = User::create([
                'name' => $request->name,
                'cedula' => $request->cedula,
                'email' => $request->email,
                'password' => Hash::make(Str::random(32)), // Contraseña temporal aleatoria
            ]);

            // Asignar el rol
            $usuario->assignRole($request->role);

            // Enviar email de reset password automáticamente
            $status = Password::sendResetLink(['email' => $usuario->email]);

            DB::commit();

            if ($status == Password::RESET_LINK_SENT) {
                return redirect()->route('usuario.index')
                    ->with('success', 'Usuario creado exitosamente. Se ha enviado un correo para establecer la contraseña.');
            } else {
                return redirect()->route('usuario.index')
                    ->with('warning', 'Usuario creado, pero no se pudo enviar el correo de configuración de contraseña. Error: ' . __($status));
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('usuario.index')
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required',
            'cedula' => 'required|unique:users,cedula,' . $usuario->id,
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'role' => 'required|exists:roles,name',
        ], [
            'cedula.unique' => 'La cédula ya está registrada.',
            'email.unique' => 'El correo ya está registrado.',
        ]);

        try {
            DB::beginTransaction();
            //Comprueba la contraseña y aplica el hash
            if (empty($request->password)) {
                $requestData = $request->except('password');
            }
            else{
                $fieldHash = Hash::make($request->password);
                $request->merge(['password' => $fieldHash]);
                $requestData = $request->all();
            }
            $usuario->update($requestData);
            //actualiza el rol
            $usuario->syncRoles($request->role);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('usuario.index')
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }

        return redirect()->route('usuario.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Reenviar email de configuración de contraseña
     */
    public function resendPasswordSetup(User $usuario)
    {
        try {
            // Verificar que el usuario esté activo
            if (!$usuario->condicion) {
                return redirect()->route('usuario.index')
                    ->with('error', 'No se puede enviar el correo a un usuario inactivo.');
            }

            // Enviar email de reset password
            $status = Password::sendResetLink(['email' => $usuario->email]);

            if ($status == Password::RESET_LINK_SENT) {
                return redirect()->route('usuario.index')
                    ->with('success', 'Correo de configuración de contraseña reenviado exitosamente a ' . $usuario->email);
            } else {
                return redirect()->route('usuario.index')
                    ->with('error', 'No se pudo enviar el correo. Error: ' . __($status));
            }
            
        } catch (\Exception $e) {
            return redirect()->route('usuario.index')
                ->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        // Proteger al superadmin inborrable
        if ($usuario->name === 'Super Administrador') {
            return redirect()->route('usuario.index')->with('error', 'No puedes eliminar ni inactivar el usuario principal del sistema.');
        }

        // Cambia el estado de condicion (activo/inactivo)
        $usuario->condicion = !$usuario->condicion;
        $usuario->save();

        $mensaje = $usuario->condicion
            ? 'Usuario activado exitosamente.'
            : 'Usuario inactivado exitosamente.';

        return redirect()->route('usuario.index')->with('success', $mensaje);
    }
}
