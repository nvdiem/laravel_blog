@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Posts</h4>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
        + Create Post
    </a>
</div>

{{-- ===== STATUS TABS ===== --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}"
           href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => ''])) }}">
            All <span class="badge bg-secondary">{{ $allCount }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'published' ? 'active' : '' }}"
           href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'published'])) }}">
            Published <span class="badge bg-success">{{ $publishedCount }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'draft' ? 'active' : '' }}"
           href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'draft'])) }}">
            Draft <span class="badge bg-secondary">{{ $draftCount }}</span>
        </a>
    </li>
</ul>

{{-- ===== FILTERS ===== --}}
<form method="GET" id="filter-form" class="row g-3 mb-3">
    <div class="col-md-3">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search posts..."
               value="{{ request('search') }}"
               style="height: 30px; font-size: 13px; border: 1px solid #d1d5db; border-radius: 4px; background-color: #f9fafb; color: #374151;">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="category_id" class="form-select form-select-sm">
            <option value="">All Categories</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="tag_id" class="form-select form-select-sm">
            <option value="">All Tags</option>
            @foreach($tags as $tag)
            <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
    </div>
    <div class="col-md-2 text-end">
        @if(request()->hasAny(['search', 'status', 'category_id', 'tag_id']))
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
        @endif
    </div>
</form>

<hr class="mt-2 mb-3">

{{-- ===== ALERTS ===== --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger py-2">
    <ul class="mb-0 small">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ===== TABLE ===== --}}
<div class="table-responsive">
    <table class="table table-sm table-hover align-middle shadow-sm rounded">
        <thead class="table-light text-muted small">
            <tr>
                <th width="50">ID</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_dir' => request('sort_by') === 'title' && request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-muted">
                        Title
                        @if(request('sort_by') === 'title')
                            {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </a>
                </th>
                <th>Slug</th>
                <th>Category</th>
                <th>Tags</th>
                <th width="90">Thumbnail</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => request('sort_by') === 'status' && request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-muted">
                        Status
                        @if(request('sort_by') === 'status')
                            {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr class="post-row">
                <td class="text-muted">{{ $post->id }}</td>

                <td class="fw-medium">
                    {{ $post->title }}
                    <div class="row-actions" style="display: none;">
                        <a href="{{ route('admin.posts.edit', $post) }}">Edit</a> |
                        <a href="{{ route('admin.posts.show', $post) }}">View</a>
                        @if(auth()->user()->isAdmin())
                        | <a href="#" class="text-danger" onclick="if(confirm('Delete this post?')) { event.preventDefault(); document.getElementById('delete-form-{{ $post->id }}').submit(); }">Trash</a>
                        @endif
                    </div>
                </td>

                <td class="text-muted small">
                    {{ $post->slug }}
                </td>

                <td>
                    {{ $post->category?->name ?? 'Uncategorized' }}
                </td>

                <td>
                    @forelse($post->tags as $tag)
                        <span class="badge bg-secondary-subtle text-secondary border me-1 small">
                            {{ $tag->name }}
                        </span>
                    @empty
                        <span class="text-muted small">—</span>
                    @endforelse
                </td>

                <td>
                    @if($post->thumbnail)
                        <img
                            src="{{ asset('storage/' . $post->thumbnail) }}"
                            class="rounded border"
                            style="width: 45px; height: 45px; object-fit: cover;"
                            alt="Thumbnail"
                        >
                    @else
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                             style="width: 45px; height: 45px;">
                            <span class="text-muted small">—</span>
                        </div>
                    @endif
                </td>

                <td>
                    @if($post->status === 'published')
                        <span class="badge bg-success-subtle text-success border border-success">
                            Published
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border">
                            Draft
                        </span>
                    @endif
                </td>

                @if(auth()->user()->isAdmin())
                <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No posts found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($posts->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} results
    </div>
    <div>
        {{ $posts->links() }}
    </div>
</div>
@endif


@endsection
