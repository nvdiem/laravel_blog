<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} — Dashboard</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body>

<!-- ===== ADMIN SIDEBAR ===== -->
<div class="admin-sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="{{ url('/admin') }}">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span> {{ config('app.name') }}
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Content</div>
            <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                Posts
            </a>
            <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-file"></i></span>
                Pages
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-tags"></i></span>
                Categories
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Leads</div>
            <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                Leads
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Analytics</div>
            <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                Analytics
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Media</div>
            <a href="{{ route('admin.media.index') }}" class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-images"></i></span>
                Media Library
            </a>
        </div>

        @can('user.manage')
        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                Users
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                Roles
            </a>
            @can('system.configure')
            <a href="{{ route('admin.site-settings.index') }}" class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                Site Settings
            </a>
            @endcan
        </div>
        @endcan
    </nav>

    {{-- <div class="sidebar-footer">
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
    </div> --}}

</div>

<div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- ===== ADMIN MAIN ===== -->
    <div class="admin-main">
        <!-- ===== ADMIN NAVBAR ===== -->
        <nav class="navbar navbar-light bg-white admin-navbar">
            <div class="container-fluid">
                <button class="navbar-toggler d-md-none me-2" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-text mb-0 fw-semibold">
                    {{ $title ?? 'Dashboard' }}
                </span>

                <div class="d-flex gap-2">
                    <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Visit Blog
                    </a>
                </div>
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
{{ config('app.name') }}
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function to open media library modal
function openMediaLibrary() {
    const modal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
    modal.show();
}

// Sidebar toggle functions
function toggleSidebar() {
    document.querySelector('.admin-sidebar').classList.toggle('open');
    document.querySelector('.sidebar-overlay').classList.toggle('show');
}

function closeSidebar() {
    document.querySelector('.admin-sidebar').classList.remove('open');
    document.querySelector('.sidebar-overlay').classList.remove('show');
}
</script>

@stack('scripts')

<!-- Media Library Modal -->
@include('admin.media.modal')
</body>
</html>
