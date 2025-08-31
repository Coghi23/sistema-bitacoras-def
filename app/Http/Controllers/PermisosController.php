<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermisosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'ver');
        $busqueda = $request->get('busquedaPermiso');
        $query = Permission::query();
        if ($busqueda) {
            $query->where('name', 'like', "%$busqueda%");
        }
        $tipos = [
            'ver' => 'view_',
            'crear' => 'create_',
            'editar' => 'edit_',
            'eliminar' => 'delete_',
        ];
        if (isset($tipos[$tab])) {
            $query->where('name', 'like', $tipos[$tab] . '%');
        } elseif ($tab === 'otros') {
            $query->where(function($q) use ($tipos) {
                foreach ($tipos as $pref) {
                    $q->where('name', 'not like', $pref . '%');
                }
            });
        }
        $permisos = $query->orderBy('name')->paginate(10)->appends(['tab' => $tab, 'busquedaPermiso' => $busqueda]);
        return view('Permisos.index', compact('permisos', 'tab'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Permisos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);
        $original = $request->name;
        $name = $this->normalizePermissionName($original);
        Permission::create(['name' => $name]);
        $msg = 'Permiso creado correctamente';
        if ($name !== strtolower(trim($original))) {
            $msg .= ". El nombre fue estandarizado a: '$name'";
        }
        return redirect()->route('permisos.index')->with('success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permiso = Permission::findOrFail($id);
        return view('Permisos.show', compact('permiso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permiso = Permission::findOrFail($id);
        return view('Permisos.edit', compact('permiso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permiso = Permission::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);
        $original = $request->name;
        $name = $this->normalizePermissionName($original);
        $permiso->update(['name' => $name]);
        $msg = 'Permiso actualizado correctamente';
        if ($name !== strtolower(trim($original))) {
            $msg .= ". El nombre fue estandarizado a: '$name'";
        }
        return redirect()->route('permisos.index')->with('success', $msg);
    }
    /**
     * Normaliza el nombre del permiso a un formato estándar.
     */
    private function normalizePermissionName($name)
    {
        $name = strtolower($name);
        $name = trim($name);
        $name = preg_replace('/[\s_]+/', '_', $name); // espacios y guiones bajos a uno solo

        // Corrección de errores comunes de tipeo para prefijos
        $typoMap = [
            // ver
            'ver' => ['ver', 'veer', 'verr', 'verr_', 'ver_', 'verr '],
            // crear
            'crear' => ['crear', 'crearr', 'crea', 'creaar', 'crearr_', 'crear_', 'crearr '],
            // editar
            'editar' => ['editar', 'edita', 'editarr', 'edtar', 'edtir', 'editr', 'edita_', 'editar_', 'editarr_'],
            // eliminar
            'eliminar' => ['eliminar', 'elimiar', 'elminar', 'elimnar', 'eliminar_', 'elimiar_', 'eliminarr', 'eliminarr_'],
        ];
        $prefixes = [
            'ver' => 'view_',
            'view' => 'view_',
            'crear' => 'create_',
            'create' => 'create_',
            'editar' => 'edit_',
            'edit' => 'edit_',
            'eliminar' => 'delete_',
            'delete' => 'delete_',
        ];

        // Detectar y corregir errores de tipeo en el prefijo
        $prefixFound = false;
        foreach ($typoMap as $correct => $variants) {
            foreach ($variants as $variant) {
                if (preg_match('/^' . preg_quote($variant, '/') . '(_|\s)/', $name)) {
                    // Reemplazar por el prefijo correcto
                    $name = preg_replace('/^' . preg_quote($variant, '/') . '(_|\s)*/', $prefixes[$correct], $name);
                    $prefixFound = true;
                    break 2;
                }
            }
        }
        // Si no se detectó error de tipeo, usar los prefijos estándar
        if (!$prefixFound) {
            foreach ($prefixes as $key => $std) {
                if (preg_match('/^' . $key . '(_|\s)/', $name)) {
                    $name = preg_replace('/^' . $key . '(_|\s)*/', $std, $name);
                    $prefixFound = true;
                    break;
                }
            }
        }

        // Estandarizar nombres de recursos multi-palabra (ej: tipo recinto, tipo_recinto, Tipo_recinto)
        if ($prefixFound) {
            // Si hay prefijo, separar el resto
            if (preg_match('/^(view_|create_|edit_|delete_)(.+)$/', $name, $matches)) {
                $resource = $matches[2];
                $resource = preg_replace('/[\s_]+/', '_', $resource); // unificar separadores
                $resource = preg_replace('/_{2,}/', '_', $resource);
                $resource = trim($resource, '_');
                $name = $matches[1] . $resource;
            }
        } else {
            // Si no hay prefijo, estandarizar todo
            $name = preg_replace('/[\s_]+/', '_', $name);
            $name = preg_replace('/_{2,}/', '_', $name);
            $name = trim($name, '_');
        }
        return $name;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permiso = Permission::findOrFail($id);
        $permiso->delete();
        return redirect()->route('permisos.index')->with('success', 'Permiso eliminado correctamente');
    }
}
