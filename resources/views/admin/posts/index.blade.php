@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Posts</h4>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
        + Create Post
    </a>
</div>

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
                <th>Title</th>
                <th>Slug</th>
                <th>Category</th>
                <th>Tags</th>
                <th width="90">Thumbnail</th>
                <th>Status</th>
                <th width="170">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr>
                <td class="text-muted">{{ $post->id }}</td>

                <td class="fw-medium">
                    {{ $post->title }}
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
                            style="width: 60px; height: 40px; object-fit: cover;"
                            alt="Thumbnail"
                        >
                    @else
                        <span class="text-muted small">No image</span>
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

                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.posts.show', $post) }}"
                           class="btn btn-sm btn-outline-secondary">
                            Show
                        </a>

                        <a href="{{ route('admin.posts.edit', $post) }}"
                           class="btn btn-sm btn-outline-warning">
                            Edit
                        </a>

                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('admin.posts.destroy', $post) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
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
