<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: \App\Models\SiteSetting::get('site_name', config('app.name')) }}</title>
    <meta name="description" content="{{ $seoDescription ?? \App\Models\SiteSetting::get('seo_description') }}">

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
                     alt="{{ \App\Models\SiteSetting::get('site_name', 'Laravel Blog') }}"
                     class="me-2"
                     style="height: 32px; width: auto; max-width: 120px;">
            @endif
            {{ \App\Models\SiteSetting::get('site_name', 'Antigravity') }}
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
            © {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'Laravel Blog') }}. All rights reserved.
        </p>
        <small>
            Built for developers.
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
