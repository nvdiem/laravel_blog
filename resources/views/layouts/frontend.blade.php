<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $seoTitle ?? \App\Models\SiteSetting::get('seo_title') ?: \App\Models\SiteSetting::get('site_name', config('app.name')) }}</title>
    <meta name="description" content="{{ $seoDescription ?? \App\Models\SiteSetting::get('seo_description') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --tech-accent: {{ \App\Models\SiteSetting::get('primary_color', '#6366f1') }};
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont,
                "Segoe UI", Roboto, Inter, Arial, sans-serif;
            background-color: #f8fafc;
        }

        /* ===== NAVBAR ===== */
        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .navbar-brand span {
            color: var(--tech-accent);
        }

        /* ===== HERO ===== */
        .hero {
            padding: 4rem 0 3rem;
            text-align: center;
        }

        .hero h1 {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .hero p {
            max-width: 640px;
            margin: 1rem auto 0;
            color: #6b7280;
        }

        /* ===== CARD ===== */
        .card {
            border: none;
            border-radius: 10px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* ===== TAG / BADGE ===== */
        .tech-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }

        /* ===== FOOTER ===== */
        footer {
            font-size: 14px;
        }
    </style>

    @stack('head')
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-xl">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            @if(\App\Models\SiteSetting::get('site_logo'))
                <img src="{{ asset('storage/' . \App\Models\SiteSetting::get('site_logo')) }}"
                     alt="{{ \App\Models\SiteSetting::get('site_name', 'Laravel Blog') }}"
                     class="me-2"
                     style="height: 32px; width: auto; max-width: 120px;">
            @else
                <span class="me-2"></span>
            @endif
            {{ \App\Models\SiteSetting::get('site_name', 'Laravel Blog') }}
        </a>

        <div class="navbar-nav ms-auto align-items-center gap-3">
            <a class="nav-link" href="{{ url('/') }}">Home</a>

            @auth
                <a class="nav-link" href="{{ url('/admin') }}">Admin</a>
                <span class="navbar-text small text-light">
                    {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        Logout
                    </button>
                </form>
            @else
                <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ===== HERO ===== --}}
<section class="hero bg-white border-bottom">
    <div class="container-xl">
        <h1>Engineering Notes on Backend, System Design & AI</h1>
        <p>
            Practical articles about web development, clean architecture,
            performance, and modern software engineering.
        </p>
    </div>
</section>

{{-- ===== MAIN CONTENT ===== --}}
<main class="py-5">
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer class="bg-dark text-light py-4">
    <div class="container text-center">
        <p class="mb-1">
            © {{ date('Y') }} Laravel Blog
        </p>
        <small class="text-muted">
            Crafted for developers, by developers.
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
