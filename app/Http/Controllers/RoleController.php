<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->has('inactivos')) {
            $query->where('activo', 0); // O el campo que uses para estado
        } else {
            $query->where('activo', 1);
        }

        $roles = Role::with('permissions')->get();
        $permisos = Permission::all();
        return view('Role.index', compact('roles', 'permisos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('Role.create', compact('roles'));
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
            $role = Role::create(['name' => strtolower($request->name)]);
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Rol creado correctamente');
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
        $role = Role::findOrFail($id);
        $permisos = Permission::all();
        return view('Role.edit', compact('role', 'permisos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);
        try {
            DB::beginTransaction();

            $role->update(['name' => strtolower($request->name)]);
            //obtener los permisos por ID
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            //actualizar permisos
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Rol actualizado correctamente');
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
        $role = Role::findOrFail($id);
        //Alternar estado
        $role->condicion = $role->condicion ? 0 : 1;
        $role->save();

        $message = $role->condicion ? 'Rol reactivado correctamente!' : 'Rol desactivado correctamente!';
        return redirect()->route('role.index')->with('success', $message);
    }
}
