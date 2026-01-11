<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class InstallService
{
    protected $stateFile = 'install_state.json';
    protected $lockFile = 'install.lock';

    /**
     * Check if application is already installed
     */
    public function isInstalled(): bool
    {
        return SiteSetting::get('app_installed', false);
    }

    /**
     * Check if installation is locked
     */
    public function isLocked(): bool
    {
        return Storage::disk('local')->exists($this->lockFile);
    }

    /**
     * Lock installation process
     */
    public function lock(): void
    {
        Storage::disk('local')->put($this->lockFile, now()->toISOString());
    }

    /**
     * Unlock installation process
     */
    public function unlock(): void
    {
        Storage::disk('local')->delete($this->lockFile);
    }

    /**
     * Get current installation state
     */
    public function getState(): array
    {
        if (!Storage::disk('local')->exists($this->stateFile)) {
            return [
                'step' => 1,
                'completed_steps' => [],
                'data' => []
            ];
        }

        return json_decode(Storage::disk('local')->get($this->stateFile), true);
    }

    /**
     * Save installation state
     */
    public function saveState(array $state): void
    {
        Storage::disk('local')->put($this->stateFile, json_encode($state, JSON_PRETTY_PRINT));
    }

    /**
     * Clear installation state
     */
    public function clearState(): void
    {
        Storage::disk('local')->delete($this->stateFile);
    }

    /**
     * Step 1: Environment check
     */
    public function checkEnvironment(): array
    {
        $checks = [
            'php_version' => [
                'name' => 'PHP Version',
                'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'message' => 'PHP 8.1.0 or higher required',
                'current' => PHP_VERSION
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'status' => extension_loaded('pdo'),
                'message' => 'PDO extension required'
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'status' => extension_loaded('mbstring'),
                'message' => 'Mbstring extension required'
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'status' => extension_loaded('openssl'),
                'message' => 'OpenSSL extension required'
            ],
            'tokenizer' => [
                'name' => 'Tokenizer Extension',
                'status' => extension_loaded('tokenizer'),
                'message' => 'Tokenizer extension required'
            ],
            'xml' => [
                'name' => 'XML Extension',
                'status' => extension_loaded('xml'),
                'message' => 'XML extension required'
            ],
            'ctype' => [
                'name' => 'Ctype Extension',
                'status' => extension_loaded('ctype'),
                'message' => 'Ctype extension required'
            ],
            'json' => [
                'name' => 'JSON Extension',
                'status' => extension_loaded('json'),
                'message' => 'JSON extension required'
            ],
            'storage_writable' => [
                'name' => 'Storage Directory Writable',
                'status' => is_writable(storage_path()),
                'message' => 'Storage directory must be writable'
            ],
            'content_writable' => [
                'name' => 'Content Directory Writable',
                'status' => is_writable(public_path('content')) || mkdir(public_path('content'), 0755, true),
                'message' => 'Content directory must be writable'
            ]
        ];

        $passed = collect($checks)->every(fn($check) => $check['status']);

        return [
            'passed' => $passed,
            'checks' => $checks
        ];
    }

    /**
     * Step 2: Database configuration
     */
    public function configureDatabase(array $data): bool
    {
        try {
            // Test database connection
            DB::connection()->getPdo();

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Database configuration failed: ' . $e->getMessage());
        }
    }

    /**
     * Step 3: Create admin user
     */
    public function createAdminUser(array $data): User
    {
        try {
            DB::beginTransaction();

            // Create admin user
            $user = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            // Assign created_by explicitly (no mass assignment)
            $user->created_by = $user->id;
            $user->save();

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create admin user: ' . $e->getMessage());
        }
    }

    /**
     * Step 4: Setup roles and permissions
     */
    public function setupRolesAndPermissions(): void
    {
        try {
            DB::beginTransaction();

            // Create permissions
            $permissions = [
                ['name' => 'user.manage', 'description' => 'Manage users'],
                ['name' => 'role.manage', 'description' => 'Manage roles'],
                ['name' => 'permission.manage', 'description' => 'Manage permissions'],
                ['name' => 'post.manage', 'description' => 'Manage posts'],
                ['name' => 'page.manage', 'description' => 'Manage pages'],
                ['name' => 'category.manage', 'description' => 'Manage categories'],
                ['name' => 'media.manage', 'description' => 'Manage media'],
                ['name' => 'system.configure', 'description' => 'Configure system settings'],
                ['name' => 'analytics.view', 'description' => 'View analytics'],
            ];

            foreach ($permissions as $perm) {
                Permission::create($perm);
            }

            // Create roles
            $adminRole = Role::create([
                'name' => 'admin',
                'description' => 'Administrator',
            ]);

            $editorRole = Role::create([
                'name' => 'editor',
                'description' => 'Editor',
            ]);

            // Assign permissions to admin role
            $adminPermissions = Permission::all();
            $adminRole->permissions()->attach($adminPermissions->pluck('id'));

            // Assign permissions to editor role
            $editorPermissions = Permission::whereIn('name', [
                'post.manage',
                'page.manage',
                'category.manage',
                'media.manage'
            ])->get();
            $editorRole->permissions()->attach($editorPermissions->pluck('id'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to setup roles and permissions: ' . $e->getMessage());
        }
    }

    /**
     * Step 5: Setup site settings
     */
    public function setupSiteSettings(array $data): void
    {
        try {
            SiteSetting::set('site_name', $data['site_name'] ?? 'Laravel Blog');
            SiteSetting::set('site_description', $data['site_description'] ?? 'A modern blog built with Laravel');
            SiteSetting::set('site_url', url('/'));
            SiteSetting::set('admin_email', $data['admin_email']);
            SiteSetting::set('app_installed', true);
            SiteSetting::set('app_version', '1.0.0');
            SiteSetting::set('installed_at', now()->toISOString());
        } catch (\Exception $e) {
            throw new \Exception('Failed to setup site settings: ' . $e->getMessage());
        }
    }

    /**
     * Step 6: Create content directory structure
     */
    public function createContentDirectories(): void
    {
        try {
            $directories = [
                'content/uploads',
                'content/themes',
                'content/plugins',
                'content/backups'
            ];

            foreach ($directories as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Create .htaccess for security
            $htaccess = public_path('content/.htaccess');
            if (!file_exists($htaccess)) {
                $content = "# Security: Deny direct access to content files\n";
                $content .= "Order Deny,Allow\n";
                $content .= "Deny from all\n";
                $content .= "Allow from 127.0.0.1\n";
                $content .= "Allow from localhost\n";
                file_put_contents($htaccess, $content);
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to create content directories: ' . $e->getMessage());
        }
    }

    /**
     * Complete installation
     */
    public function completeInstallation(): void
    {
        $this->clearState();
        $this->unlock();
    }

    /**
     * Get installation steps
     */
    public function getSteps(): array
    {
        return [
            1 => ['name' => 'Environment Check', 'description' => 'Checking system requirements'],
            2 => ['name' => 'Database Setup', 'description' => 'Configuring database and running migrations'],
            3 => ['name' => 'Admin User', 'description' => 'Creating administrator account'],
            4 => ['name' => 'Roles & Permissions', 'description' => 'Setting up user roles and permissions'],
            5 => ['name' => 'Site Configuration', 'description' => 'Configuring basic site settings'],
            6 => ['name' => 'Content Setup', 'description' => 'Creating content directory structure'],
        ];
    }
}
