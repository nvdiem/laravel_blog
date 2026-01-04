<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): View
    {
        $this->authorize('role.manage');

        $roles = Role::with('users', 'permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $this->authorize('role.manage');

        $permissions = config('permissions.modules');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('role.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        // Clean and validate permissions - remove empty values and duplicates
        $submittedPermissions = array_filter(array_map('trim', $validated['permissions'] ?? []));

        // Validate permissions are from whitelist
        $allowedPermissions = collect(config('permissions.modules'))
            ->map(fn($module) => array_keys($module))
            ->flatten()
            ->toArray();
        $invalidPermissions = array_diff($submittedPermissions, $allowedPermissions);



        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        // Assign permissions
        if (!empty($validated['permissions'])) {
            $role->permissions()->sync(
                Permission::whereIn('slug', $validated['permissions'])->pluck('id')
            );
        }

        // Audit log
        AuditLog::log(
            'create',
            $role,
            null,
            ['name' => $role->name, 'permissions' => $validated['permissions'] ?? []],
            "Created role '{$role->name}' with " . count($validated['permissions'] ?? []) . " permissions"
        );

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show the form for editing a role.
     */
    public function edit(Role $role): View
    {
        $this->authorize('role.manage');

        // Prevent editing system roles
        if (in_array($role->slug, config('permissions.system_roles'))) {
            abort(403, 'System roles cannot be edited.');
        }

        $permissions = config('permissions.modules');
        $rolePermissions = $role->permissions->pluck('slug')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('role.manage');

        // Prevent editing system roles
        if (in_array($role->slug, config('permissions.system_roles'))) {
            abort(403, 'System roles cannot be edited.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        // Clean and validate permissions - remove empty values and duplicates
        $submittedPermissions = array_filter(array_map('trim', $validated['permissions'] ?? []));

        // Validate permissions are from whitelist
        $allowedPermissions = collect(config('permissions.modules'))
            ->map(fn($module) => array_keys($module))
            ->flatten()
            ->toArray();
        $invalidPermissions = array_diff($submittedPermissions, $allowedPermissions);

        if (!empty($invalidPermissions)) {
            return back()->withErrors(['permissions' => 'Invalid permissions selected.'])->withInput();
        }

        // Get old values for audit
        $oldPermissions = $role->permissions->pluck('slug')->toArray();

        // Update role
        $role->update([
            'name' => $validated['name'],
        ]);

        // Update permissions
        $newPermissions = $validated['permissions'] ?? [];
        $role->permissions()->sync(
            Permission::whereIn('slug', $newPermissions)->pluck('id')
        );

        // Audit log
        AuditLog::log(
            'update',
            $role,
            ['name' => $role->getOriginal('name'), 'permissions' => $oldPermissions],
            ['name' => $role->name, 'permissions' => $newPermissions],
            "Updated role '{$role->name}': permissions changed from " . count($oldPermissions) . " to " . count($newPermissions)
        );

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role.manage');

        // Prevent deleting system roles
        if (in_array($role->slug, config('permissions.system_roles'))) {
            return back()->withErrors(['role' => 'System roles cannot be deleted.']);
        }

        // Check if role is assigned to any users
        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Cannot delete role that is assigned to users.']);
        }

        $roleName = $role->name;
        $rolePermissions = $role->permissions->pluck('slug')->toArray();

        // Audit log before deletion
        AuditLog::log(
            'delete',
            $role,
            ['name' => $role->name, 'permissions' => $rolePermissions],
            null,
            "Deleted role '{$role->name}' with " . count($rolePermissions) . " permissions"
        );

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }
}
