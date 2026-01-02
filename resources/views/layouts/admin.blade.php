<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin · Laravel Blog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --tech-accent: #0d6efd;
        }

        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont,
                "Segoe UI", Roboto, Inter, Arial, sans-serif;
        }

        /* ===== NAVBAR ===== */
        .admin-navbar {
            min-height: 48px;
        }

        .admin-title {
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .admin-title span {
            color: var(--tech-accent);
        }

        /* ===== CONTENT ===== */
        .admin-content {
            padding: 24px;
            border-top: 1px solid #e5e7eb;
        }

        /* ===== TABLE (ADMIN STYLE) ===== */
        table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        thead th {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            background-color: #f9fafb;
        }

        tbody td {
            vertical-align: middle;
        }

        /* ===== FOOTER ===== */
        footer.admin-footer {
            font-size: 12px;
            color: #6c757d;
        }
    </style>

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
