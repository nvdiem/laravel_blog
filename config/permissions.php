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
            'post.create' => 'Create Posts',
            'post.edit_own' => 'Edit Own Posts',
            'post.edit_any' => 'Edit Any Post',
            'post.delete_own' => 'Delete Own Posts',
            'post.delete_any' => 'Delete Any Post',
            'post.publish' => 'Publish Posts',
            'post.bulk_action' => 'Bulk Actions',
        ],

        'taxonomy' => [
            'taxonomy.manage' => 'Manage Taxonomy (Categories & Tags)',
        ],

        'media' => [
            'media.upload' => 'Upload Media',
            'media.delete' => 'Delete Media',
        ],

        'users' => [
            'user.manage' => 'Manage Users',
            'role.manage' => 'Manage Roles',
        ],

        'system' => [
            'system.configure' => 'System Configuration',
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
