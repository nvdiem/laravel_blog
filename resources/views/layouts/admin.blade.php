<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin · Laravel Blog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">



    @stack('head')
</head>
<body>

<!-- ===== ADMIN NAVBAR ===== -->
<nav class="navbar navbar-dark bg-dark admin-navbar">
    <div class="container-fluid">
        <a class="navbar-brand admin-title" href="{{ route('admin.posts.index') }}">
            Admin · <span>Laravel Blog</span>
        </a>

        @auth
        <div class="d-flex align-items-center text-white">
            <span class="me-3 small">👋 {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>

<!-- ===== CONTENT ===== -->
<main class="container-fluid admin-content">
    @yield('content')
</main>

<!-- ===== FOOTER ===== -->
<footer class="border-top py-3 bg-white admin-footer">
    <div class="container-fluid text-center">
        Laravel Blog · Admin Panel
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
