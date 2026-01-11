<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(config('brand.assets.favicon.ico')) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(config('brand.assets.favicon.png_16')) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset(config('brand.assets.favicon.png_32')) }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset(config('brand.assets.app_icons.apple_touch')) }}">

    <!-- Primary Meta Tags -->
    <title>{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: config('brand.meta.title') }}</title>
    <meta name="title" content="{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: config('brand.meta.title') }}">
    <meta name="description" content="{{ $seoDescription ?? \App\Models\SiteSetting::get('seo_description') ?: config('brand.meta.description') }}">
    <meta name="keywords" content="{{ config('brand.meta.keywords') }}">
    <meta name="author" content="{{ config('brand.meta.author') }}">
    <meta name="robots" content="{{ config('brand.meta.robots') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ config('brand.social.og.type') }}">
    <meta property="og:site_name" content="{{ config('brand.social.og.site_name') }}">
    <meta property="og:title" content="{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: config('brand.social.og.title') }}">
    <meta property="og:description" content="{{ $seoDescription ?? \App\Models\SiteSetting::get('seo_description') ?: config('brand.social.og.description') }}">
    <meta property="og:image" content="{{ config('brand.social.og.image') }}">
    <meta property="og:image:width" content="{{ config('brand.social.og.image_width') }}">
    <meta property="og:image:height" content="{{ config('brand.social.og.image_height') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="{{ config('brand.social.twitter.card') }}">
    <meta property="twitter:site" content="{{ config('brand.social.twitter.site') }}">
    <meta property="twitter:creator" content="{{ config('brand.social.twitter.creator') }}">
    <meta property="twitter:title" content="{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: config('brand.social.twitter.title') }}">
    <meta property="twitter:description" content="{{ $seoDescription ?? \App\Models\SiteSetting::get('seo_description') ?: config('brand.social.twitter.description') }}">
    <meta property="twitter:image" content="{{ config('brand.social.twitter.image') }}">

    {{-- FONTS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container-xl">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            @if(\App\Models\SiteSetting::get('site_logo'))
                <img src="{{ asset('storage/' . \App\Models\SiteSetting::get('site_logo')) }}"
                     alt="{{ \App\Models\SiteSetting::get('site_name', config('brand.name')) }}"
                     class="me-2"
                     style="height: 32px; width: auto; max-width: 120px;">
            @else
                <img src="{{ asset(config('brand.assets.logo.icon')) }}"
                     alt="{{ config('brand.name') }}"
                     class="me-2"
                     style="height: 24px; width: 24px;">
            @endif
            {{ \App\Models\SiteSetting::get('site_name', config('brand.name')) }}
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <div class="navbar-nav ms-auto align-items-center gap-2">
                <a class="nav-link" href="{{ url('/') }}">Home</a>
                <a class="nav-link" href="#">About</a> {{-- Placeholder --}}
                
                @auth
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-link fw-bold text-primary" href="{{ url('/admin') }}">Dashboard</a>
                @else
                    <a class="btn btn-dark btn-sm rounded-pill px-4 ms-2" href="{{ route('login') }}">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ===== CURRENT PAGE HERO (Optional) ===== --}}
@yield('hero')

{{-- ===== MAIN CONTENT ===== --}}
<main class="py-5 bg-transparent">
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer>
    <div class="container text-center text-muted">
        <p class="mb-2">
            {{ config('brand.footer.copyright') }}
        </p>
        <small>
            {{ config('brand.tagline') }}
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
