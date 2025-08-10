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
        $usuarios = User::with('roles')->get();
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
        $user = User::find($id);
        $rolUser = $user->getRoleNames()->first();
        $user->delete();

        return redirect()->route('usuario.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
