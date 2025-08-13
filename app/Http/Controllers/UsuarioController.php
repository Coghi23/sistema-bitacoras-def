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
            'cedula' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required|exists:roles,name',
        ]);
        try {
            DB::beginTransaction();

            // Hashea la contraseña
            $fieldHash = Hash::make($request->password);

            // Crea el usuario solo con los campos válidos
            $usuario = User::create([
                'name' => $request->name,
                'cedula' => $request->cedula,
                'email' => $request->email,
                'password' => $fieldHash,
                // agrega aquí otros campos reales de tu tabla users si los tienes
            ]);

            // Asigna el rol
            $usuario->assignRole($request->role);

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('usuario.index')
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
            
        return redirect()->route('usuario.index')->with('success', 'Usuario creado exitosamente.');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
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
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('usuario.index')
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }

        return redirect()->route('usuario.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        // Cambia el estado de condicion (activo/inactivo)
        $usuario->condicion = !$usuario->condicion;
        $usuario->save();

        $mensaje = $usuario->condicion
            ? 'Usuario activado exitosamente.'
            : 'Usuario inactivado exitosamente.';

        return redirect()->route('usuario.index')->with('success', $mensaje);
    }
}
