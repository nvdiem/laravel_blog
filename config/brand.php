<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized branding configuration for PointOne CMS.
    | This ensures consistent branding across the entire application.
    |
    */

    'name' => env('BRAND_NAME', 'PointOne'),

    'tagline' => env('BRAND_TAGLINE', 'Một nền tảng cho mọi website.'),

    /*
    |--------------------------------------------------------------------------
    | Brand Assets
    |--------------------------------------------------------------------------
    |
    | Paths to brand assets. All paths are relative to public/ directory.
    |
    */

    'assets' => [
        'logo' => [
            'horizontal' => 'brand/pointone/logo-horizontal.svg',
            'icon' => 'brand/pointone/logo-icon.svg',
        ],

        'favicon' => [
            'ico' => 'brand/pointone/favicon.ico',
            'png_16' => 'brand/pointone/favicon-16.png',
            'png_32' => 'brand/pointone/favicon-32.png',
        ],

        // 'app_icons' => [
        //     'apple_touch' => 'brand/pointone/apple-touch-icon.png', // 180x180
        //     'icon_512' => 'brand/pointone/icon-512.png',
        // ],
        // Temporarily commented out until assets are available

        'social' => [
            'og_image' => 'brand/pointone/og-1200x630.png', // 1200x630
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand Colors
    |--------------------------------------------------------------------------
    |
    | Color palette for the brand.
    |
    */

    'colors' => [
        'primary' => '#2563EB',    // Blue
        'accent' => '#00DC82',    // Mint
        'text' => '#0F172A',      // Navy
        'muted' => '#64748B',     // Slate
        'light' => '#F8FAFC',     // Light background
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Meta Information
    |--------------------------------------------------------------------------
    |
    | Default meta tags for SEO and social sharing.
    |
    */

    'meta' => [
        'title' => env('BRAND_META_TITLE', 'PointOne - Một nền tảng cho mọi website'),
        'description' => env('BRAND_META_DESCRIPTION', 'Nền tảng CMS hiện đại giúp bạn tạo và quản lý website chuyên nghiệp một cách dễ dàng. Tích hợp đầy đủ tính năng cho blog, trang landing page và hệ thống quản lý nội dung.'),
        'keywords' => 'CMS, website builder, Laravel, PointOne, quản lý nội dung',
        'author' => 'PointOne Team',
        'robots' => 'index, follow',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Meta
    |--------------------------------------------------------------------------
    |
    | Open Graph and Twitter Card defaults.
    |
    */

    'social' => [
        'og' => [
            'type' => 'website',
            'site_name' => env('BRAND_NAME', 'PointOne'),
            'title' => env('BRAND_META_TITLE', 'PointOne - Một nền tảng cho mọi website'),
            'description' => env('BRAND_META_DESCRIPTION', 'Nền tảng CMS hiện đại giúp bạn tạo và quản lý website chuyên nghiệp một cách dễ dàng.'),
            'image' => env('APP_URL', 'https://pointone.vn') . '/brand/pointone/og-1200x630.png',
            'image_width' => 1200,
            'image_height' => 630,
        ],

        'twitter' => [
            'card' => 'summary_large_image',
            'site' => '@pointonevn',
            'creator' => '@pointonevn',
            'title' => env('BRAND_META_TITLE', 'PointOne - Một nền tảng cho mọi website'),
            'description' => env('BRAND_META_DESCRIPTION', 'Nền tảng CMS hiện đại giúp bạn tạo và quản lý website chuyên nghiệp một cách dễ dàng.'),
            'image' => env('APP_URL', 'https://pointone.vn') . '/brand/pointone/og-1200x630.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer Information
    |--------------------------------------------------------------------------
    */

    'footer' => [
        'copyright' => '© ' . date('Y') . ' PointOne. All rights reserved.',
        'links' => [
            'about' => 'https://pointone.vn/about',
            'contact' => 'https://pointone.vn/contact',
            'privacy' => 'https://pointone.vn/privacy',
            'terms' => 'https://pointone.vn/terms',
        ],
    ],

];
