<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $query = Role::with('permissions');
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        }
        $roles = $query->orderBy('name')->get();
        $selected = $id ? Role::with('permissions')->findOrFail($id) : new Role();
        $permissions = Permission::all()->groupBy('module');

        $selectedPermissionIds = $selected->id ? $selected->permissions->pluck('id')->toArray() : [];

        return view('system.role_management', compact('roles', 'selected', 'permissions', 'selectedPermissionIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $request->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $data['name'] = mb_strtoupper(trim($request->name), 'UTF-8');

        if ($request->id) {
            $role = Role::findOrFail($request->id);
            if ($role->is_system || strtoupper($role->name) === 'ADMIN') {
                unset($data['name']); // Protect ADMIN name from modification
            }
            $role->update($data);
            $role->permissions()->sync($request->input('permissions', []));
            return redirect()->route('system.role.load', $role->id)->with('success', 'Role updated successfully.');
        }

        $role = Role::create($data);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('system.role.load', $role->id)->with('success', 'Role created successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system || strtoupper($role->name) === 'ADMIN') {
            return redirect()->route('system.role')->with('error', 'System protected role (ADMIN) cannot be deleted.');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('system.role')->with('success', 'Role deleted successfully.');
    }
}
