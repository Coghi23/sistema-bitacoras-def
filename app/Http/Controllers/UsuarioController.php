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
        $users = User::all();
        return view('Usuario.index', compact('users'));
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

        //password hash
        $fieldHash = Hash::make($request->password);
        //Modify the password value in request
        $request->merge(['password' => $fieldHash]);

        //create user
        $user = User::create($request->all());

        //Asign rol
        $user->assignRole($request->role);

        DB::commit();
    } 
    catch (Exception $e) {
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
                $requrestData = $request->except('password');
            }
            else{
                $fieldHash = Hash::make($request->password);
                $request->merge(['password' => $fieldHash]);
                $requestData = $request->all();
            }
            $user->update($requestData);
            //actualiza el rol
            $user->syncRoles($request->role);
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
