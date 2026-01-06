<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Upload Configuration
    |--------------------------------------------------------------------------
    */

    'upload' => [
        // Maximum file size in kilobytes (configurable via .env)
        // Default: 5120 KB (5 MB)
        'max_size' => env('MEDIA_MAX_SIZE', 5120),
        
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'document' => ['pdf', 'doc', 'docx'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */

    'storage' => [
        'disk' => 'public',
        'path' => 'media', // Organized as: media/YYYY/MM/filename.ext
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Configuration
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'per_page' => 24, // Grid view items per page
    ],

];
