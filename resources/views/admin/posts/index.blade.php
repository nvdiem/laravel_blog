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

{{-- ===== FILTERS (GIỮ NGUYÊN) ===== --}}
<form method="GET" id="filter-form" class="row g-3 mb-3">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control"
               placeholder="Search posts..." value="{{ request('search') }}">
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
        @if(request()->hasAny(['search','status','category_id','tag_id']))
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                Clear Filters
            </a>
        @endif
    </div>
</form>

{{-- ===================================================== --}}
{{-- ===== BULK ACTION FORM (FIXED – GIỮ UI CŨ) ===== --}}
{{-- ===================================================== --}}
<form method="POST"
      action="{{ route('admin.posts.bulk') }}"
      id="bulk-form">
    @csrf

    <div class="row g-3 mb-3 align-items-center">
        <div class="col-md-2">
            {{-- 🔧 FIX 1: name="action" --}}
            <select name="action" class="form-select form-select-sm" id="bulk-action-select">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="delete">Move to Trash</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit"
                    class="btn btn-outline-secondary btn-sm"
                    id="bulk-apply-btn"
                    >
                Apply
            </button>
        </div>
        <div class="col-md-9">
            <small class="text-muted">
                Select posts using the checkboxes, then choose an action above.
            </small>
        </div>
    </div>

    {{-- ===== ALERTS ===== --}}
    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ===================================================== --}}
    {{-- ===== TABLE (CHỈ DI CHUYỂN VÀO TRONG FORM) ===== --}}
    {{-- ===================================================== --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle shadow-sm rounded">
            <thead class="table-light text-muted small">
                <tr>
                    <th width="40">
                        <input type="checkbox" id="select-all-checkbox">
                    </th>
                    <th width="50">ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th width="90">Thumbnail</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        {{-- 🔧 FIX 2: checkbox nằm trong form --}}
                        <input type="checkbox"
                               class="form-check-input row-checkbox"
                               name="post_ids[]"
                               value="{{ $post->id }}">
                    </td>
                    <td class="text-muted">{{ $post->id }}</td>
                    <td class="fw-medium">{{ $post->title }}</td>
                    <td class="text-muted small">{{ $post->slug }}</td>
                    <td>{{ $post->primaryCategory()?->name ?? 'Uncategorized' }}</td>
                    <td>
                        @foreach($post->tags as $tag)
                            <span class="badge bg-secondary-subtle text-secondary border small">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </td>
                    <td>
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/'.$post->thumbnail) }}"
                                 class="rounded border"
                                 style="width:45px;height:45px;object-fit:cover;">
                        @endif
                    </td>
                    <td>
                        @if($post->status === 'published')
                            <span class="badge bg-success-subtle text-success border">
                                Published
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">
                                Draft
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No posts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

{{-- ===== PAGINATION (GIỮ NGUYÊN) ===== --}}
@if($posts->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} results
    </div>
    <div>{{ $posts->links() }}</div>
</div>
@endif

@endsection
