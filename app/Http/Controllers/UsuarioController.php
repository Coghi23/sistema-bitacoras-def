<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institucione;
use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles']);
        
        // Búsqueda por nombre, email o cédula
        if ($request->filled('busquedaUsuario')) {
            $busqueda = $request->busquedaUsuario;
            $query->where(function($q) use ($busqueda) {
                $q->where('name', 'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%")
                  ->orWhere('cedula', 'like', "%{$busqueda}%");
            });
        }

        $usuarios = $query->latest()->get();
        $roles = Role::all();
        $instituciones = Institucione::all();
        $especialidades = Especialidade::all();

        return view('Usuario.index', compact('usuarios', 'roles', 'instituciones', 'especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cedula' => 'required|string|max:35|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'password' => Hash::make($request->password),
            'condicion' => true,
        ]);

        // Asignar rol al usuario
        $user->assignRole($request->rol);

        event(new Registered($user));

        return redirect()->route('usuario.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'cedula' => 'required|string|max:35|unique:users,cedula,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|exists:roles,name',
            'condicion' => 'required|boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'condicion' => $request->condicion,
        ];

        // Solo actualizar password si se proporciona uno nuevo
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $usuario->update($updateData);

        // Sincronizar roles
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuario.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        // No permitir eliminar al usuario actual
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuario.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Cambiar condición a false en lugar de eliminar
        $usuario->update(['condicion' => false]);

        return redirect()->route('usuario.index')
            ->with('eliminado', 'Usuario desactivado exitosamente.');
    }
}
