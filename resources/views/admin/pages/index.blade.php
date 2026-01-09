@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="posts-page-header d-flex justify-content-between align-items-center">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Pages</h1>
    @can('create', \App\Models\Page::class)
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
        <i class="fas fa-plus fa-xs"></i> Add New Page
    </a>
    @endcan
</div>

{{-- ===== STATUS LINKS (ROW 1) ===== --}}
<div class="wp-status-filters mb-2">
    <a href="{{ route('admin.pages.index', array_merge(request()->query(), ['status' => ''])) }}"
       class="{{ !request('status') ? 'current' : '' }}">
        All <span class="count">({{ $allCount }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.pages.index', array_merge(request()->query(), ['status' => 'published'])) }}"
       class="{{ request('status') === 'published' ? 'current' : '' }}">
        Published <span class="count">({{ $publishedCount }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.pages.index', array_merge(request()->query(), ['status' => 'draft'])) }}"
       class="{{ request('status') === 'draft' ? 'current' : '' }}">
        Draft <span class="count">({{ $draftCount }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.pages.index', array_merge(request()->query(), ['status' => 'disabled'])) }}"
       class="{{ request('status') === 'disabled' ? 'current' : '' }}">
        Disabled
    </a>
</div>

{{-- ===== TOOLBAR (BULK ACTIONS + FILTERS + SEARCH) (ROW 2) ===== --}}
<div class="bulk-actions-container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2 flex-wrap">
        {{-- Bulk Actions --}}
        <form method="POST" action="#" id="bulk-form" class="d-flex gap-2 align-items-center">
            @csrf
            <select name="action" class="form-select form-select-sm w-auto" id="bulk-action-select">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="disable">Disable</option>
                <option value="delete">Move to Trash</option>
            </select>
            <button type="submit" class="btn btn-outline-secondary btn-sm" id="bulk-apply-btn">Apply</button>
        </form>

        {{-- Search --}}
        <form method="GET" class="d-flex gap-1 align-items-center mb-0 ms-2" style="max-width: 200px;">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search pages..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
        </form>

        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.pages.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 ms-1 text-danger">
                Clear
            </a>
        @endif
    </div>

    {{-- Pagination Summary (Right) --}}
    <div class="text-muted small">
        {{ $pages->total() }} items
    </div>
</div>

{{-- ===== ALERTS ===== --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center small mb-3">
    <i class="fas fa-check-circle me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger d-flex align-items-center small mb-3">
    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger small mb-3">
    <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
</div>
@endif

{{-- ===== TABLE ===== --}}
<div class="table-responsive posts-table admin-table-container">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Updated</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
            <tr class="post-row">
                <td class="title-column">
                    <div class="post-title">
                        <a href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }}</a>
                    </div>
                </td>
                <td class="fw-monospace small text-muted">
                    {{ $page->slug }}
                </td>
                <td>
                    @if($page->status === 'published')
                        <span class="badge-soft badge-soft-success">Published</span>
                    @elseif($page->status === 'draft')
                        <span class="badge-soft badge-soft-warning">Draft</span>
                    @else
                        <span class="badge-soft">Disabled</span>
                    @endif
                </td>
                <td class="date-column">
                    <span class="text-muted">{{ $page->updated_at->format('M j, Y') }}</span>
                </td>
                {{-- <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($page->storage_path)
                            <button class="btn btn-outline-info btn-sm" title="Upload New Bundle"
                                    onclick="openUploadModal({{ $page->id }}, '{{ addslashes($page->title) }}')">
                                <i class="fas fa-upload"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-primary btn-sm" title="Upload Bundle"
                                    onclick="openUploadModal({{ $page->id }}, '{{ addslashes($page->title) }}')">
                                <i class="fas fa-upload"></i>
                            </button>
                        @endif
                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this page?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td> --}}

                <td class="text-center">
    <div class="admin-actions justify-content-center">
        
        <a href="{{ route('admin.pages.edit', $page) }}"
           class="action-btn"
           title="Edit">
            <i class="fas fa-pen"></i>
        </a>

        <button class="action-btn"
                title="Upload Bundle"
                onclick="openUploadModal({{ $page->id }}, '{{ addslashes($page->title) }}')">
            <i class="fas fa-upload"></i>
        </button>

        <form method="POST"
              action="{{ route('admin.pages.destroy', $page) }}"
              class="m-0"
              onsubmit="return confirm('Are you sure you want to delete this page?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="action-btn action-delete"
                    title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>

    </div>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="admin-empty">
                        <div class="admin-empty-icon">
                            <i class="fas fa-file"></i>
                        </div>
                        <h6 class="admin-empty-title">No pages yet</h6>
                        <p class="admin-empty-description">Create your first page to get started.</p>
                        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add New Page
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($pages->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $pages->firstItem() }} to {{ $pages->lastItem() }} of {{ $pages->total() }} pages
    </div>
    <div>
        {{ $pages->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

@endsection

{{-- ===== UPLOAD BUNDLE MODAL ===== --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Page Bundle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="bundleFile" class="form-label">ZIP Bundle File</label>
                        <input type="file" class="form-control" id="bundleFile" name="bundle" accept=".zip" required>
                        <div class="form-text">
                            Upload a ZIP file containing your page assets. Must include index.html at root level.
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Uploading a new bundle will overwrite the current page content.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadBtn">Upload Bundle</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPageId = null;

function openUploadModal(pageId, pageTitle) {
    currentPageId = pageId;
    document.getElementById('uploadModalLabel').textContent = `Upload Bundle for "${pageTitle}"`;
    document.getElementById('uploadForm').action = `/admin/pages/${pageId}/upload`;
    document.getElementById('bundleFile').value = '';
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

document.getElementById('uploadBtn').addEventListener('click', function() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('bundleFile');

    if (!fileInput.files[0]) {
        alert('Please select a ZIP file to upload.');
        return;
    }

    // Show loading state
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

    // Submit form
    form.submit();
});
</script>
@endpush
