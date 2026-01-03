<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin · Laravel Blog</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">



    @stack('head')
</head>
<body>

<!-- ===== ADMIN SIDEBAR ===== -->
<div class="admin-sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
            <span>📝</span> Laravel Blog
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Content</div>
            <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <span class="nav-icon">📄</span>
                Posts
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="nav-icon">🏷️</span>
                Categories
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Media</div>
            <a href="#" class="nav-link" onclick="openMediaLibrary()">
                <span class="nav-icon">📎</span>
                Media Library
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        @auth
        <div class="user-info">
            <div class="user-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</div>

<!-- ===== ADMIN MAIN ===== -->
<div class="admin-main">
    <!-- ===== ADMIN NAVBAR ===== -->
    <nav class="navbar navbar-light bg-white admin-navbar">
        <div class="container-fluid">
            <span class="navbar-text mb-0 fw-semibold">
                {{ $title ?? 'Admin Panel' }}
            </span>
        </div>
    </nav>

    <!-- ===== CONTENT ===== -->
    <main class="admin-content">
        @yield('content')
    </main>
</div>

<!-- ===== FOOTER ===== -->
<footer class="border-top py-3 bg-white admin-footer">
    <div class="container-fluid text-center">
        Laravel Blog · Admin Panel
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function to open media library modal
function openMediaLibrary() {
    const modal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
    modal.show();
}
</script>

@stack('scripts')

<!-- Media Library Modal -->
@include('admin.media.modal')
</body>
</html>
