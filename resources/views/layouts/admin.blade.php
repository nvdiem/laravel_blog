<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Primary Meta Tags -->
    <title>{{ config('brand.meta.title') }} - {{ $title ?? 'Dashboard' }}</title>
    <meta name="title" content="{{ config('brand.meta.title') }}">
    <meta name="description" content="{{ config('brand.meta.description') }}">
    <meta name="keywords" content="{{ config('brand.meta.keywords') }}">
    <meta name="author" content="{{ config('brand.meta.author') }}">
    <meta name="robots" content="{{ config('brand.meta.robots') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ config('brand.social.og.type') }}">
    <meta property="og:site_name" content="{{ config('brand.social.og.site_name') }}">
    <meta property="og:title" content="{{ config('brand.social.og.title') }}">
    <meta property="og:description" content="{{ config('brand.social.og.description') }}">
    <meta property="og:image" content="{{ config('brand.social.og.image') }}">
    <meta property="og:image:width" content="{{ config('brand.social.og.image_width') }}">
    <meta property="og:image:height" content="{{ config('brand.social.og.image_height') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="{{ config('brand.social.twitter.card') }}">
    <meta property="twitter:site" content="{{ config('brand.social.twitter.site') }}">
    <meta property="twitter:creator" content="{{ config('brand.social.twitter.creator') }}">
    <meta property="twitter:title" content="{{ config('brand.social.twitter.title') }}">
    <meta property="twitter:description" content="{{ config('brand.social.twitter.description') }}">
    <meta property="twitter:image" content="{{ config('brand.social.twitter.image') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(config('brand.assets.favicon.ico')) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(config('brand.assets.favicon.png_16')) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset(config('brand.assets.favicon.png_32')) }}">


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
            <span class="nav-icon">
                <img src="{{ asset(config('brand.assets.logo.icon')) }}" alt="{{ config('brand.name') }}" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">
            </span>
            {{ config('brand.name') }}
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
        <small class="text-muted">{{ config('brand.footer.copyright') }}</small>
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
