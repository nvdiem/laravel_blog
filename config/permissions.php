<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Whitelist
    |--------------------------------------------------------------------------
    |
    | System-defined permissions that can be assigned to roles.
    | These are grouped by module for better organization.
    |
    */

    'modules' => [
        'posts' => [
            'post.create' => [
                'label' => 'Create Posts',
                'description' => 'Can create new blog posts and save them as drafts'
            ],
            'post.edit_own' => [
                'label' => 'Edit Own Posts',
                'description' => 'Can edit posts that they created themselves'
            ],
            'post.edit_any' => [
                'label' => 'Edit Any Post',
                'description' => 'Can edit all posts created by any user'
            ],
            'post.delete_own' => [
                'label' => 'Delete Own Posts',
                'description' => 'Can delete posts that they created themselves'
            ],
            'post.delete_any' => [
                'label' => 'Delete Any Post',
                'description' => 'Can delete all posts created by any user'
            ],
            'post.publish' => [
                'label' => 'Publish Posts',
                'description' => 'Can publish draft posts to make them live'
            ],
            'post.bulk_action' => [
                'label' => 'Bulk Post Actions',
                'description' => 'Can perform bulk operations like mass publish or delete'
            ],
        ],

        'taxonomy' => [
            'taxonomy.manage' => [
                'label' => 'Manage Categories & Tags',
                'description' => 'Can create, edit, and delete categories and tags'
            ],
        ],

        'media' => [
            'media.upload' => [
                'label' => 'Upload Media Files',
                'description' => 'Can upload images and other media files'
            ],
            'media.delete' => [
                'label' => 'Delete Media Files',
                'description' => 'Can delete uploaded media files'
            ],
        ],

        'users' => [
            'user.manage' => [
                'label' => 'Manage User Accounts',
                'description' => 'Can view, create, and modify user accounts'
            ],
            'role.manage' => [
                'label' => 'Manage User Roles',
                'description' => 'Can assign and modify user roles and permissions'
            ],
        ],

        'system' => [
            'system.configure' => [
                'label' => 'System Configuration',
                'description' => 'Can modify system settings and configuration'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | System Roles
    |--------------------------------------------------------------------------
    |
    | Roles that cannot be edited or deleted through the UI.
    | These are core system roles.
    |
    */

    'system_roles' => [
        'super_admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Dependencies
    |--------------------------------------------------------------------------
    |
    | Some permissions require others to function properly.
    | For example, edit_own might be required for edit_any to make sense.
    |
    */

    'dependencies' => [
        // No dependencies defined yet
    ],
];
