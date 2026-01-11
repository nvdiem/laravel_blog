@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    {{-- ===== WELCOME HEADER ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted mb-0">Here's what's happening with your blog today.</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== NOTICES ===== --}}
    @if($notices)
        @foreach($notices as $notice)
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ $notice }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endforeach
    @endif

    {{-- ===== QUICK STATS ===== --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Posts</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_posts']) }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-file-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Published</h6>
                            <h3 class="mb-0">{{ number_format($stats['published_posts']) }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-left-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Drafts</h6>
                            <h3 class="mb-0">{{ number_format($stats['draft_posts']) }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-edit fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Categories</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_categories']) }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-tags fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="row">

        {{-- ===== RECENT ACTIVITY ===== --}}
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentPosts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPosts as $post)
                            <div class="list-group-item px-3 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-decoration-none">
                                                {{ Str::limit($post->title, 60) }}
                                            </a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span>
                                                @if($post->status === 'published')
                                                    <span class="badge bg-success-subtle text-success">Published</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Draft</span>
                                                @endif
                                            </span>
                                            @if($post->primaryCategory && $post->primaryCategory->first())
                                            <span>
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $post->primaryCategory->first()->name }}
                                            </span>
                                            @endif
                                            <span>
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $post->updated_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No recent activity</h5>
                            <p class="text-muted mb-0">Start by creating your first post!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== QUICK ACTIONS ===== --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Post
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tags me-2"></i>Manage Categories
                        </a>
                        @can('user.manage')
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                        @endcan
                        <button onclick="openMediaLibrary()" class="btn btn-outline-secondary">
                            <i class="fas fa-images me-2"></i>Open Media Library
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>View All Posts
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== SYSTEM INFO ===== --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Info</h6>
                </div>
                <div class="card-body small">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-semibold">{{ number_format($stats['total_users']) }}</div>
                            <div class="text-muted">Users</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">{{ number_format($stats['total_posts']) }}</div>
                            <div class="text-muted">Total Posts</div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center text-muted">
                        <small>{{ config('brand.name') }} CMS v1.0</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.border-left-primary {
    border-left: 4px solid #2271b1 !important;
}
.border-left-success {
    border-left: 4px solid #1e8c5e !important;
}
.border-left-warning {
    border-left: 4px solid #dba617 !important;
}
.border-left-info {
    border-left: 4px solid #72aee6 !important;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-outline-primary:hover {
    color: #ffffff;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center h2 {
        font-size: 1.5rem;
    }
}
</style>
@endsection
