@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Media Library</h1>
    <button class="btn btn-primary d-inline-flex align-items-center gap-1" onclick="openUploadModal()">
        <i class="fas fa-upload fa-xs"></i> Upload Files
    </button>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="bulk-actions-container media-toolbar d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        {{-- View Toggle --}}
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary active" id="grid-view-btn" title="Grid View">
                <i class="fas fa-th"></i>
            </button>
            <button class="btn btn-outline-secondary" id="list-view-btn" title="List View">
                <i class="fas fa-list"></i>
            </button>
        </div>

        {{-- Filters --}}
        <form method="GET" class="d-flex gap-2 align-items-center mb-0" id="filter-form">
            <select name="type" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">All file types</option>
                <option value="images" {{ request('type') === 'images' ? 'selected' : '' }}>Images only</option>
            </select>

            <select name="usage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">All items</option>
                <option value="used" {{ request('usage') === 'used' ? 'selected' : '' }}>Used</option>
                <option value="unused" {{ request('usage') === 'unused' ? 'selected' : '' }}>Unused</option>
            </select>

            @if(request()->hasAny(['search', 'type', 'usage']))
                <a href="{{ route('admin.media.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 ms-1 text-danger">
                    Clear
                </a>
            @endif
        </form>

        {{-- Search --}}
        <form method="GET" class="d-flex gap-1 align-items-center mb-0 ms-2" style="max-width: 200px;">
            @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
            @if(request('usage'))<input type="hidden" name="usage" value="{{ request('usage') }}">@endif
            <input type="search" name="search" class="form-control form-control-sm" 
                   placeholder="Search files..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="text-muted small">
        {{ $totalCount }} items • {{ number_format($totalSize / 1024 / 1024, 2) }} MB total
    </div>
</div>

{{-- ===== ALERTS ===== --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center small mb-3 mt-3">
    <i class="fas fa-check-circle me-2 text-success"></i> 
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger small mb-3 mt-3">
    <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
</div>
@endif

{{-- ===== GRID VIEW (Default) ===== --}}
<div id="media-grid" class="media-grid mt-3">
    @forelse($media as $item)
        <div class="media-item media-card" data-id="{{ $item->id }}">
            <div class="media-thumbnail">
                @if($item->isImage())
                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->file_name }}" loading="lazy">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fas fa-file fa-3x text-muted"></i>
                    </div>
                @endif
            </div>
            <div class="media-info">
                <div class="filename text-truncate" title="{{ $item->file_name }}">
                    {{ $item->file_name }}
                </div>
                <div class="meta text-muted small">
                    {{ $item->dimensions ?? '—' }} • {{ $item->formatted_size }}
                </div>
            </div>
            <div class="media-actions mt-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="copyUrl('{{ $item->url }}')" title="Copy URL">
                    <i class="fas fa-link"></i>
                </button>
                @if($item->isImage())
                <button class="btn btn-sm btn-outline-secondary" onclick="viewFullSize('{{ $item->url }}', '{{ $item->file_name }}')" title="Preview">
                    <i class="fas fa-search-plus"></i>
                </button>
                @endif
                <button class="btn btn-sm btn-outline-danger" onclick="deleteMedia({{ $item->id }}, '{{ addslashes($item->file_name) }}')" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @empty
        <div class="admin-empty">
            <div class="admin-empty-icon">
                <i class="fas fa-images"></i>
            </div>
            <h6 class="admin-empty-title">No media files yet</h6>
            <p class="admin-empty-description">Upload your first image or document to get started.</p>
            <button class="btn btn-primary" onclick="openUploadModal()">
                <i class="fas fa-upload me-1"></i> Upload Files
            </button>
        </div>
    @endforelse
</div>

{{-- ===== LIST VIEW (Hidden) ===== --}}
<div id="media-list" class="table-responsive d-none mt-3">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th width="80">Preview</th>
                <th>Filename</th>
                <th width="120">Dimensions</th>
                <th width="100">Size</th>
                <th width="120">Uploaded</th>
                <th width="180" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($media as $item)
            <tr>
                <td>
                    @if($item->isImage())
                        <img src="{{ $item->url }}" width="60" height="45" class="border rounded" style="object-fit: cover;">
                    @else
                        <div class="text-center">
                            <i class="fas fa-file fa-2x text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <div class="fw-medium">{{ $item->file_name }}</div>
                    @if($item->alt_text)
                        <small class="text-muted">{{ $item->alt_text }}</small>
                    @endif
                </td>
                <td class="text-muted">{{ $item->dimensions ?? '—' }}</td>
                <td class="text-muted">{{ $item->formatted_size }}</td>
                <td class="text-muted">
                    {{ $item->created_at->format('Y-m-d') }}<br>
                    <small>{{ $item->created_at->format('H:i') }}</small>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="copyUrl('{{ $item->url }}')" title="Copy URL">
                            <i class="fas fa-link"></i>
                        </button>
                        @if($item->isImage())
                        <button class="btn btn-outline-secondary" onclick="viewFullSize('{{ $item->url }}', '{{ $item->file_name }}')" title="Preview">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        @endif
                        <button class="btn btn-outline-danger" onclick="deleteMedia({{ $item->id }}, '{{ addslashes($item->file_name) }}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($media->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $media->firstItem() }} to {{ $media->lastItem() }} of {{ $media->total() }} files
    </div>
    <div>
        {{ $media->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

{{-- ===== UPLOAD MODAL ===== --}}
@include('admin.media._upload_modal')

@endsection

@push('styles')
<style>
/* WordPress-style container max-width */
#media-grid,
#media-list {
    max-width: 1600px;
}

.media-grid {
    display: grid;
    /* Max 6 columns like WordPress, min 180px per item */
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
    max-width: 1600px;
}

/* Responsive breakpoints */
@media (min-width: 1600px) {
    .media-grid {
        grid-template-columns: repeat(6, 1fr); /* Max 6 columns */
    }
}

@media (max-width: 1200px) {
    .media-grid {
        grid-template-columns: repeat(4, 1fr); /* 4 columns on medium screens */
    }
}

@media (max-width: 900px) {
    .media-grid {
        grid-template-columns: repeat(3, 1fr); /* 3 columns on tablets */
    }
}

@media (max-width: 600px) {
    .media-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 columns on mobile */
    }
}

.media-item {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.media-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-color: #2271b1;
}

.media-thumbnail {
    aspect-ratio: 4/3;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f6f7f7;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 8px;
}

.media-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-info .filename {
    font-size: 13px;
    font-weight: 500;
    color: #1d2327;
    line-height: 1.4;
}

.media-info .meta {
    font-size: 12px;
    margin-top: 4px;
}

.media-actions {
    display: flex;
    gap: 4px;
}

.media-actions .btn {
    flex: 1;
}

/* Lightbox Dialog */
dialog {
    border: none;
    border-radius: 8px;
    padding: 0;
    max-width: 90vw;
    max-height: 90vh;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

dialog::backdrop {
    background: rgba(0, 0, 0, 0.8);
}

dialog img {
    max-width: 100%;
    max-height: 90vh;
    display: block;
}
</style>
@endpush

@push('scripts')
<script>
// View Toggle
document.getElementById('grid-view-btn').onclick = function() {
    document.getElementById('media-grid').classList.remove('d-none');
    document.getElementById('media-list').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('list-view-btn').classList.remove('active');
};

document.getElementById('list-view-btn').onclick = function() {
    document.getElementById('media-list').classList.remove('d-none');
    document.getElementById('media-grid').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('grid-view-btn').classList.remove('active');
};

// Copy URL to clipboard
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        // Create temporary success message
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="fas fa-check-circle me-2"></i>URL copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }).catch(() => {
        alert('Failed to copy URL');
    });
}

// View full size image (HTML5 dialog)
function viewFullSize(url, filename) {
    const dialog = document.createElement('dialog');
    dialog.style.cssText = 'max-width: 90vw; max-height: 90vh; padding: 0; border: none; border-radius: 8px;';
    dialog.innerHTML = `
        <div style="position: relative; background: #000;">
            <img src="${url}" alt="${filename}" style="display: block;">
            <button onclick="this.closest('dialog').close(); this.closest('dialog').remove();" 
                    style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 4px; font-size: 14px;">
                <i class="fas fa-times"></i> Close
            </button>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 12px; font-size: 13px;">
                ${filename}
            </div>
        </div>
    `;
    document.body.appendChild(dialog);
    dialog.showModal();
    
    // Close on backdrop click
    dialog.addEventListener('click', (e) => {
        if (e.target === dialog) {
            dialog.close();
            dialog.remove();
        }
    });
    
    // Close on Escape key
    dialog.addEventListener('close', () => dialog.remove());
}

// Delete media
function deleteMedia(id, filename) {
    if (!confirm(`Delete "${filename}"?\n\nThis action cannot be undone.`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/media/${id}`;
    form.innerHTML = `
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
