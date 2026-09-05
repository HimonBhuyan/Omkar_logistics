<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $query = User::with(['roles', 'primaryRole', 'companies', 'userPermissions.permission']);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($sub) use ($q) {
                $sub->where('username', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone_number', 'like', "%{$q}%");
            });
        }
        $users = $query->orderBy('name')->get();

        $selected = $id ? User::with(['roles', 'companies', 'userPermissions'])->findOrFail($id) : new User(['is_active' => true]);
        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $permissions = Permission::all()->groupBy('module');

        $selectedRoleIds = $selected->id ? $selected->roles->pluck('id')->toArray() : [];
        if ($selected->role_id && !in_array($selected->role_id, $selectedRoleIds)) {
            $selectedRoleIds[] = $selected->role_id;
        }

        $selectedCompanyIds = $selected->id ? $selected->companies->pluck('id')->toArray() : [];
        if ($selected->company_id && !in_array($selected->company_id, $selectedCompanyIds)) {
            $selectedCompanyIds[] = $selected->company_id;
        }

        // Map existing user permission overrides: [permission_id => is_granted (1 or 0)]
        $userOverrides = [];
        if ($selected->id) {
            foreach ($selected->userPermissions as $up) {
                $userOverrides[$up->permission_id] = $up->is_granted ? 'allow' : 'deny';
            }
        }

        return view('system.user_management', compact(
            'users', 'selected', 'roles', 'companies', 'permissions',
            'selectedRoleIds', 'selectedCompanyIds', 'userOverrides'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:50|unique:users,username,' . $request->id,
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:users,email,' . $request->id,
            'phone_number' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'is_active' => 'nullable|boolean',
        ];

        if (!$request->id) {
            $rules['password'] = 'required|string|min:4';
        } else {
            $rules['password'] = 'nullable|string|min:4';
        }

        $validated = $request->validate($rules);

        $companiesInput = $request->input('companies', []);
        $primaryCompanyId = !empty($companiesInput) ? $companiesInput[0] : 1;

        $data = [
            'username' => trim($request->username),
            'name' => trim($request->name),
            'email' => $request->filled('email') ? trim($request->email) : null,
            'phone_number' => $request->filled('phone_number') ? trim($request->phone_number) : null,
            'company_id' => $primaryCompanyId,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $rolesInput = $request->input('roles', []);
        if (!empty($rolesInput)) {
            $data['role_id'] = $rolesInput[0]; // Primary role
        }

        if ($request->id) {
            $user = User::findOrFail($request->id);
            unset($data['username']); // Username cannot be changed once user is created
            if (strtoupper($user->username) === 'ADMIN') {
                $data['is_active'] = true; // Cannot deactivate ADMIN
            }
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        // Sync Roles
        $user->roles()->sync($rolesInput);

        // Sync Permitted Companies
        $user->companies()->sync($companiesInput);

        // Sync 3-State Permission Overrides
        $overridesInput = $request->input('user_permissions', []);
        foreach ($overridesInput as $permId => $state) {
            if ($state === 'allow' || $state === '1') {
                UserPermission::updateOrCreate(
                    ['user_id' => $user->id, 'permission_id' => $permId],
                    ['is_granted' => true]
                );
            } elseif ($state === 'deny' || $state === '0') {
                UserPermission::updateOrCreate(
                    ['user_id' => $user->id, 'permission_id' => $permId],
                    ['is_granted' => false]
                );
            } else {
                // Inherit -> Delete direct override record
                UserPermission::where('user_id', $user->id)->where('permission_id', $permId)->delete();
            }
        }

        return redirect()->route('system.user.load', $user->id)->with('success', 'User account saved successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!$user->isDeletable()) {
            return redirect()->route('system.user')->with('error', 'System administrator user cannot be deleted.');
        }

        $user->roles()->detach();
        $user->companies()->detach();
        $user->userPermissions()->delete();
        $user->delete();

        return redirect()->route('system.user')->with('success', 'User account deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            $deletedCount = 0;
            foreach ($ids as $userId) {
                $user = User::find($userId);
                if ($user && $user->isDeletable()) {
                    $user->roles()->detach();
                    $user->companies()->detach();
                    $user->userPermissions()->delete();
                    $user->delete();
                    $deletedCount++;
                }
            }
            return redirect()->route('system.user')->with('success', "{$deletedCount} user account(s) deleted successfully.");
        }
        return redirect()->route('system.user')->with('error', 'No users selected.');
    }
}
