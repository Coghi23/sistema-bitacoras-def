<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permissions\Models\Role;
use Spatie\Permissions\Models\Permission;
use Illumintate\Support\Facades\DB;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('roles.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            // crea el rol
            $role = Role::create(['name' => $request->name]);
            //obtiene los permisospor ID
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            //sincroniza los permisos con el rol
            $role->syncPermissions($permissions);

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el rol.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permisos = Permission::all();
        return view('roles.edit', compact('role', 'permisos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);
        try {
            DB::beginTransaction();
            
            $role->update(['name' => $request->name]);
            //obtener los permisos por ID
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            //actualizar permisos
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el rol.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
