<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Storage Configuration (Mode A - Shared Hosting)
    |--------------------------------------------------------------------------
    |
    | This configuration defines the storage structure for user-generated
    | content in shared hosting environments where storage/app/public
    | may not be accessible via symlinks.
    |
    */

    'storage' => [

        /*
        |--------------------------------------------------------------------------
        | Media Storage
        |--------------------------------------------------------------------------
        |
        | All uploaded media files (images, documents) are stored in
        | public/content/uploads with organized directory structure.
        |
        */

        'media' => [
            'disk' => 'content_uploads',
            'path' => 'uploads', // Base path within disk
            'url_base' => '/content/uploads', // Public URL base
        ],

        /*
        |--------------------------------------------------------------------------
        | Page Builder Bundles
        |--------------------------------------------------------------------------
        |
        | Page builder bundles (HTML/CSS/JS) are stored in
        | public/content/page-bundles with versioned structure.
        |
        */

        'page_bundles' => [
            'disk' => 'page_bundles',
            'path' => 'page-bundles', // Base path within disk
            'url_base' => '/content/page-bundles', // Public URL base
        ],

        /*
        |--------------------------------------------------------------------------
        | Temporary Files
        |--------------------------------------------------------------------------
        |
        | Temporary files during upload/processing are stored in
        | public/content/tmp and cleaned up automatically.
        |
        */

        'temp' => [
            'disk' => 'content_tmp',
            'path' => 'tmp',
            'cleanup_after_hours' => 24, // Auto-cleanup temp files
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    'security' => [

        /*
        |--------------------------------------------------------------------------
        | Forbidden File Extensions
        |--------------------------------------------------------------------------
        |
        | Files with these extensions are blocked during upload.
        | This prevents execution of server-side scripts.
        |
        */

        'forbidden_extensions' => [
            // Server-side scripts (dangerous)
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
            'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh',

            // Executables and binaries
            'exe', 'bat', 'cmd', 'com', 'scr', 'pif', 'jar', 'msi', 'dmg',

            // System and configuration files
            'env', 'ini', 'log', 'sql', 'htaccess', 'htpasswd',

            // Archives that might contain dangerous content
            'rar', '7z', 'tar', 'gz', 'bz2', 'xz',
        ],

        /*
        |--------------------------------------------------------------------------
        | Allowed Bundle Extensions
        |--------------------------------------------------------------------------
        |
        | File extensions explicitly allowed in page builder bundles.
        | These are safe for client-side web content.
        |
        */

        'allowed_bundle_extensions' => [
            'html', 'htm', 'css', 'js', 'json', 'xml',
            'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico',
            'woff', 'woff2', 'ttf', 'eot',
            'mp3', 'mp4', 'wav', 'ogg', 'webm',
            'pdf', 'txt', 'md',
        ],

        /*
        |--------------------------------------------------------------------------
        | Forbidden MIME Types
        |--------------------------------------------------------------------------
        */

        'forbidden_mime_types' => [
            'application/x-php',
            'application/x-httpd-php',
            'text/x-php',
            'application/octet-stream', // Generic binary - requires extension check
        ],

        /*
        |--------------------------------------------------------------------------
        | Upload Limits
        |--------------------------------------------------------------------------
        */

        'upload_limits' => [
            'max_file_size' => env('CMS_MAX_FILE_SIZE', 10 * 1024 * 1024), // 10MB
            'max_bundle_size' => env('CMS_MAX_BUNDLE_SIZE', 50 * 1024 * 1024), // 50MB
            'max_files_per_upload' => env('CMS_MAX_FILES_PER_UPLOAD', 10),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility
    |--------------------------------------------------------------------------
    |
    | Support for existing data in old storage locations.
    | This will be removed in future versions.
    |
    */

    'backward_compatibility' => [
        'enabled' => env('CMS_BACKWARD_COMPATIBILITY', true),
        'old_media_disk' => 'public', // storage/app/public
        'old_bundle_disk' => 'public', // storage/app/public
    ],

];
