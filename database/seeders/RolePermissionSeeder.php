<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Post permissions
            ['name' => 'Create Posts', 'slug' => 'post.create'],
            ['name' => 'Edit Own Posts', 'slug' => 'post.edit_own'],
            ['name' => 'Edit Any Post', 'slug' => 'post.edit_any'],
            ['name' => 'Delete Own Posts', 'slug' => 'post.delete_own'],
            ['name' => 'Delete Any Post', 'slug' => 'post.delete_any'],
            ['name' => 'Publish Posts', 'slug' => 'post.publish'],
            ['name' => 'Bulk Actions', 'slug' => 'post.bulk_action'],

            // Taxonomy permissions
            ['name' => 'Manage Taxonomy', 'slug' => 'taxonomy.manage'],

            // Media permissions
            ['name' => 'Upload Media', 'slug' => 'media.upload'],
            ['name' => 'Delete Media', 'slug' => 'media.delete'],

            // System permissions
            ['name' => 'Manage Users', 'slug' => 'user.manage'],
            ['name' => 'Manage Roles', 'slug' => 'role.manage'],
            ['name' => 'System Configuration', 'slug' => 'system.configure'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // Create roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Editor', 'slug' => 'editor'],
            ['name' => 'Author', 'slug' => 'author'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super_admin')->first();
        $editor = Role::where('slug', 'editor')->first();
        $author = Role::where('slug', 'author')->first();

        // Super Admin gets all permissions
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // Editor permissions
        $editorPermissions = [
            'post.create',
            'post.edit_any',
            'post.publish',
            'post.bulk_action',
            'taxonomy.manage',
            'media.upload',
        ];
        $editor->permissions()->sync(
            Permission::whereIn('slug', $editorPermissions)->pluck('id')
        );

        // Author permissions
        $authorPermissions = [
            'post.create',
            'post.edit_own',
            'media.upload',
        ];
        $author->permissions()->sync(
            Permission::whereIn('slug', $authorPermissions)->pluck('id')
        );

        // Assign super_admin role to first user (or create if none exists)
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('super_admin')) {
            $firstUser->assignRole('super_admin');
        }
    }
}
