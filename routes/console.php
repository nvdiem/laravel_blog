<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is used for defining the console routes.
|
| To see all available commands, run: php artisan list
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:roles', function () {
    $user = User::first();

    if (!$user) {
        $this->error('No users found!');
        return;
    }

    $this->info("Testing user: {$user->name} (ID: {$user->id})");
    $this->line('---');

    // Test roles
    $this->info('User roles:');
    $roles = $user->roles->pluck('slug');
    $this->line($roles->isEmpty() ? 'None' : $roles->join(', '));
    $this->line('---');

    // Test permissions
    $this->info('User permissions:');
    $permissions = $user->roles->flatMap->permissions->pluck('slug');
    $this->line($permissions->isEmpty() ? 'None' : $permissions->join(', '));
    $this->line('---');

    // Test canDo method
    $testPermissions = [
        'post.create',
        'post.edit_own',
        'post.edit_any',
        'post.publish',
        'post.bulk_action',
        'user.manage',
        'taxonomy.manage'
    ];

    $this->info('canDo() tests:');
    foreach ($testPermissions as $perm) {
        $result = $user->canDo($perm) ? 'YES' : 'NO';
        $this->line("{$perm}: {$result}");
    }

})->purpose('Test roles and permissions for first user');

Artisan::command('check:user {email}', function ($email) {
    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error("User with email '{$email}' not found!");
        return;
    }

    $this->info("Checking user: {$user->name} ({$user->email}) - ID: {$user->id}");
    $this->line('---');

    // Test roles
    $this->info('User roles:');
    $roles = $user->roles->pluck('slug');
    $this->line($roles->isEmpty() ? 'None' : $roles->join(', '));

    // Check specifically for super_admin
    $hasSuperAdmin = $user->hasRole('super_admin');
    $this->info('Super Admin role:');
    $this->line($hasSuperAdmin ? 'YES' : 'NO');

    $this->line('---');

    // Test permissions count
    $permissions = $user->roles->flatMap->permissions->pluck('slug');
    $this->info('Total permissions:');
    $this->line($permissions->count());

    if ($permissions->count() <= 10) {
        $this->info('User permissions:');
        $this->line($permissions->isEmpty() ? 'None' : $permissions->join(', '));
    }

    $this->line('---');

    // Test key permissions
    $keyPermissions = [
        'user.manage',
        'role.manage',
        'post.create',
        'post.publish'
    ];

    $this->info('Key permissions:');
    foreach ($keyPermissions as $perm) {
        $result = $user->canDo($perm) ? 'YES' : 'NO';
        $this->line("{$perm}: {$result}");
    }

})->purpose('Check roles and permissions for specific user by email');

Artisan::command('test:user-access {email}', function ($email) {
    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error("User with email '{$email}' not found!");
        return;
    }

    $this->info("Testing access for: {$user->name} ({$user->email})");
    $this->line('---');

    // Test admin routes access
    $routes = [
        'admin.users.index' => 'user.manage',
        'admin.posts.index' => 'viewAny on Post (authenticated)',
        'admin.posts.create' => 'post.create',
    ];

    foreach ($routes as $route => $permission) {
        try {
            // Simulate authorization check
            if ($route === 'admin.users.index') {
                $canAccess = $user->canDo('user.manage');
            } elseif ($route === 'admin.posts.index') {
                $canAccess = auth()->check(); // Any authenticated user
            } elseif ($route === 'admin.posts.create') {
                $canAccess = $user->canDo('post.create');
            }

            $status = $canAccess ? '✅ ALLOWED' : '❌ DENIED';
            $this->line("{$route}: {$status} ({$permission})");

        } catch (\Exception $e) {
            $this->line("{$route}: ❌ ERROR - {$e->getMessage()}");
        }
    }

    $this->line('---');
    $this->info('Summary:');
    $this->line("User can access /admin/users: " . ($user->canDo('user.manage') ? 'YES' : 'NO'));

})->purpose('Test if user can access specific admin routes');

Artisan::command('test:gates {email}', function ($email) {
    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error("User with email '{$email}' not found!");
        return;
    }

    // Set the user as authenticated for gate testing
    auth()->login($user);

    $this->info("Testing Gates for: {$user->name} ({$user->email})");
    $this->line('---');

    // Test Gates
    $gates = [
        'user.manage',
        'role.manage',
        'system.configure',
    ];

    $this->info('Gate authorization tests:');
    foreach ($gates as $gate) {
        try {
            $allowed = Gate::allows($gate);
            $status = $allowed ? '✅ ALLOWED' : '❌ DENIED';
            $this->line("{$gate}: {$status}");
        } catch (\Exception $e) {
            $this->line("{$gate}: ❌ ERROR - {$e->getMessage()}");
        }
    }

    $this->line('---');

    // Test controller-style authorization
    $this->info('Controller authorize() simulation:');
    try {
        // This simulates what happens in UserController@index
        Gate::authorize('user.manage');
        $this->line('authorize(\'user.manage\'): ✅ PASSED');
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        $this->line('authorize(\'user.manage\'): ❌ FAILED - ' . $e->getMessage());
    } catch (\Exception $e) {
        $this->line('authorize(\'user.manage\'): ❌ ERROR - ' . $e->getMessage());
    }

    // Clean up
    auth()->logout();

})->purpose('Test Laravel Gates authorization for user');

Artisan::command('test:roles-system', function () {
    $this->info('Testing Complete Roles & Permissions System');
    $this->line('==========================================');

    // Test 1: Check permissions config
    $this->info('1. Permission Configuration:');
    $permissions = config('permissions.modules');
    $totalPerms = collect($permissions)->flatten()->count();
    $this->line("   - Total permissions: {$totalPerms}");
    $this->line('   - Modules: ' . implode(', ', array_keys($permissions)));
    $this->line('   - System roles: ' . implode(', ', config('permissions.system_roles')));
    $this->line('');

    // Test 2: Check roles in database
    $this->info('2. Database Roles:');
    $roles = \App\Models\Role::with('permissions')->get();
    foreach ($roles as $role) {
        $type = in_array($role->slug, config('permissions.system_roles')) ? 'SYSTEM' : 'CUSTOM';
        $this->line("   - {$role->name} ({$role->slug}) [{$type}]: {$role->permissions->count()} permissions");
    }
    $this->line('');

    // Test 3: Check user roles and permissions
    $this->info('3. User Role Assignment:');
    $user = \App\Models\User::first();
    if ($user) {
        $userRoles = $user->roles->pluck('slug');
        $userPerms = $user->roles->flatMap->permissions->pluck('slug')->unique();
        $this->line("   - User: {$user->name}");
        $this->line("   - Roles: " . $userRoles->join(', '));
        $this->line("   - Permissions: {$userPerms->count()} total");
    }
    $this->line('');

    // Test 4: Test authorization gates & policies
    $this->info('4. Authorization Gates:');
    auth()->login($user);
    $gates = ['user.manage', 'role.manage', 'system.configure'];
    foreach ($gates as $gate) {
        $allowed = \Illuminate\Support\Facades\Gate::allows($gate);
        $status = $allowed ? '✅ ALLOWED' : '❌ DENIED';
        $this->line("   - {$gate}: {$status}");
    }

    // Test model-based permissions (policies)
    $this->info('4b. Model-Based Permissions (Policies):');
    $policies = [
        'post.create' => 'PostPolicy@create',
        'post.publish' => 'PostPolicy@publish',
    ];
    foreach ($policies as $ability => $policy) {
        try {
            $allowed = $user->can($ability, \App\Models\Post::class);
            $status = $allowed ? '✅ ALLOWED' : '❌ DENIED';
            $this->line("   - {$ability}: {$status} ({$policy})");
        } catch (\Exception $e) {
            $this->line("   - {$ability}: ❌ ERROR - {$e->getMessage()}");
        }
    }
    auth()->logout();
    $this->line('');

    // Test 5: Check audit logs
    $this->info('5. Audit Logging:');
    $auditCount = \App\Models\AuditLog::count();
    $this->line("   - Total audit entries: {$auditCount}");
    if ($auditCount > 0) {
        $latest = \App\Models\AuditLog::latest()->first();
        $this->line("   - Latest action: {$latest->action} on {$latest->model_type}");
    }
    $this->line('');

    $this->info('✅ Roles & Permissions System Test Complete!');
})->purpose('Comprehensive test of the roles and permissions system');
