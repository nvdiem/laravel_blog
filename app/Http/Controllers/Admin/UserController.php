<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request): View
    {
        $this->authorize('user.manage');

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Update user's role assignment.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('role.manage');

        $validated = $request->validate([
            'role_slug' => 'required|string|in:super_admin,editor,author',
        ]);

        $roleSlug = $validated['role_slug'];

        // Prevent user from changing their own role
        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        // Prevent removing the last super_admin
        if ($user->hasRole('super_admin') && $roleSlug !== 'super_admin') {
            $superAdminCount = User::whereHas('roles', function ($query) {
                $query->where('slug', 'super_admin');
            })->count();

            if ($superAdminCount <= 1) {
                return back()->withErrors(['role' => 'Cannot remove the last super admin.']);
            }
        }

        // Sync the role (replace all existing roles with the new one)
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return back()->with('success', "Role updated for user {$user->name}.");
    }
}
