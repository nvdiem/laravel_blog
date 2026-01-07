@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="posts-page-header d-flex justify-content-between align-items-center">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Posts</h1>
    @can('create', \App\Models\Post::class)
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
        <i class="fas fa-plus fa-xs"></i> Add New Post
    </a>
    @endcan
</div>

{{-- ===== STATUS LINKS (ROW 1) ===== --}}
<div class="wp-status-filters mb-2">
    <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => ''])) }}" 
       class="{{ !request('status') ? 'current' : '' }}">
        All <span class="count">({{ $allCount }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>
    
    <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'published'])) }}" 
       class="{{ request('status') === 'published' ? 'current' : '' }}">
        Published <span class="count">({{ $publishedCount }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'review'])) }}" 
       class="{{ request('status') === 'review' ? 'current' : '' }}">
        Review
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'approved'])) }}" 
       class="{{ request('status') === 'approved' ? 'current' : '' }}">
        Approved
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['status' => 'draft'])) }}" 
       class="{{ request('status') === 'draft' ? 'current' : '' }}">
        Draft <span class="count">({{ $draftCount }})</span>
    </a>
</div>

{{-- ===== TOOLBAR (BULK ACTIONS + FILTERS + SEARCH) (ROW 2) ===== --}}
<div class="bulk-actions-container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2 flex-wrap">
        {{-- Bulk Actions --}}
        <form method="POST" action="{{ route('admin.posts.bulk') }}" id="bulk-form" class="d-flex gap-2 align-items-center">
            @csrf
            <select name="action" class="form-select form-select-sm w-auto" id="bulk-action-select">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="delete">Move to Trash</option>
            </select>
            <button type="submit" class="btn btn-outline-secondary btn-sm" id="bulk-apply-btn">Apply</button>
        </form>

        {{-- Filters --}}
        <form method="GET" id="filter-form" class="d-flex gap-2 align-items-center mb-0 ms-2">
            <select name="date" class="form-select form-select-sm w-auto">
                <option value="">All Dates</option>
                @foreach($dates as $date)
                    <option value="{{ $date }}" {{ request('date') == $date ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $date)->format('F Y') }}
                    </option>
                @endforeach
            </select>

            <select name="category_id" class="form-select form-select-sm w-auto">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
            
            @if(request()->hasAny(['search','status','category_id','tag_id', 'date']))
                <a href="{{ route('admin.posts.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 ms-1 text-danger">
                    Clear
                </a>
            @endif
        </form>

        {{-- Search (Moved here) --}}
        <form method="GET" class="d-flex gap-1 align-items-center mb-0 ms-2" style="max-width: 200px;">
            <input type="text" name="search" class="form-control form-control-sm" 
                   placeholder="Search posts..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
        </form>
    </div>
    
    {{-- Pagination Summary (Right) --}}
    <div class="text-muted small">
        {{ $posts->total() }} items
    </div>
</div>

{{-- ===== ALERTS ===== --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center small mb-3">
        <i class="fas fa-check-circle me-2 text-success"></i> 
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger m-2 py-1 px-3 small">
        {{ $errors->first() }}
    </div>
@endif

{{-- ===== TABLE ===== --}}
<div class="table-responsive posts-table">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="text-center">
                    <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                </th>
                <th class="title-column">Title</th>
                <th>Author</th>
                <th>Categories</th>
                <th>Tags</th>
                <th class="comments-column text-center">
                    <i class="far fa-comments" title="Comments"></i>
                </th>
                <th class="views-column">Views</th>
                <th class="date-column">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr class="post-row">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input row-checkbox" name="post_ids[]" value="{{ $post->id }}" form="bulk-form">
                </td>
                <td class="title-column position-relative">
                    <div class="post-title">
                        <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                        @if($post->status !== 'published')
                            <span class="status-text ms-2 badge badge-{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                        @endif
                    </div>
                    <div class="row-actions">
                        <a href="{{ route('admin.posts.edit', $post) }}">Edit</a> |
                        <a href="{{ route('admin.posts.show', $post) }}" target="_blank">View</a> |
                        <a href="#" class="text-danger" onclick="if(confirm('Delete post?')) { document.getElementById('delete-form-{{ $post->id }}').submit(); return false; }">Trash</a>

                        <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-none">
                            @method('DELETE')
                            @csrf
                        </form>
                    </div>
                </td>
                <td class="author-column">
                    <a href="#">{{ $post->author->name ?? 'Admin' }}</a>
                </td>
                <td class="categories-column">
                    {{ $post->primaryCategory->first()?->name ?? '—' }}
                </td>
                <td class="tags-column">
                    @forelse($post->tags->take(3) as $tag)
                        {{ $tag->name }}{{ !$loop->last ? ', ' : '' }}
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </td>
                <td class="comments-column text-center">
                    <span class="badge bg-light text-dark border">0</span>
                </td>
                <td class="views-column">
                    {{ number_format($post->views_count ?? 0) }}
                </td>
                <td class="date-column">
                    @if($post->status === 'published' && $post->published_at)
                         Published<br>
                         <span class="text-muted">{{ $post->published_at->format('M j, Y') }}</span>
                    @else
                         Modified<br>
                         <span class="text-muted">{{ $post->updated_at->format('M j, Y') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <div class="empty-state">
                        <i class="fas fa-file-alt fa-3x mb-3 opacity-25"></i>
                        <h6>No posts found</h6>
                        <p class="mb-0">Create your first post to get started.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-2 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        {{ $posts->total() }} items
    </div>
    <div>
        {{ $posts->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>


@endsection

{{-- ===== BULK ACTION CONFIRMATION MODAL ===== --}}
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Confirm Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage">Are you sure you want to perform this action?</p>
                <div class="alert alert-warning" id="bulkActionWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" id="bulkActionConfirmBtn" data-bs-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ===== WORDPRESS-STYLE BULK ACTIONS UX =====
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionSelect = document.getElementById('bulk-action-select');
    const bulkApplyBtn = document.getElementById('bulk-apply-btn');
    const bulkForm = document.getElementById('bulk-form');

    if (!selectAllCheckbox || !bulkActionSelect || !bulkApplyBtn) return;

    // ===== HEADER CHECKBOX BEHAVIOR =====
    // Handle select all checkbox click
    selectAllCheckbox.addEventListener('change', function () {
        // Toggle all visible row checkboxes
        rowCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = selectAllCheckbox.checked;
            }
        });

        updateBulkActionButton();
        updateHeaderCheckboxState();
    });

    // ===== INDIVIDUAL ROW CHECKBOXES =====
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            updateHeaderCheckboxState();
            updateBulkActionButton();
        });
    });

    // ===== BULK ACTION SELECT =====
    bulkActionSelect.addEventListener('change', function () {
        updateBulkActionButton();
    });

    // ===== FORM SUBMISSION WITH MODAL CONFIRMATION =====
    bulkForm.addEventListener('submit', function(e) {
        const action = bulkActionSelect.value;
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;

        // Double-check: prevent submission if no posts selected
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one post to perform this action.');
            return;
        }

        // Double-check: prevent submission if no action selected
        if (!action) {
            e.preventDefault();
            alert('Please select a bulk action to perform.');
            return;
        }

        // Prevent default form submission and show modal
        e.preventDefault();

        // Configure modal based on action
        const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        const modalTitle = document.getElementById('bulkActionModalLabel');
        const modalMessage = document.getElementById('bulkActionMessage');
        const modalWarning = document.getElementById('bulkActionWarning');
        const confirmBtn = document.getElementById('bulkActionConfirmBtn');

        // Set modal content based on action
        if (action === 'delete') {
            modalTitle.textContent = 'Move to Trash';
            modalMessage.textContent = `Are you sure you want to move ${checkedCount} post${checkedCount > 1 ? 's' : ''} to trash?`;
            modalWarning.style.display = 'block';
            confirmBtn.textContent = 'Move to Trash';
            confirmBtn.className = 'btn btn-danger';
        } else if (action === 'publish') {
            modalTitle.textContent = 'Publish Posts';
            modalMessage.textContent = `Are you sure you want to publish ${checkedCount} post${checkedCount > 1 ? 's' : ''}?`;
            modalWarning.style.display = 'none';
            confirmBtn.textContent = 'Publish';
            confirmBtn.className = 'btn btn-success';
        } else if (action === 'draft') {
            modalTitle.textContent = 'Move to Draft';
            modalMessage.textContent = `Are you sure you want to move ${checkedCount} post${checkedCount > 1 ? 's' : ''} to draft?`;
            modalWarning.style.display = 'none';
            confirmBtn.textContent = 'Move to Draft';
            confirmBtn.className = 'btn btn-warning';
        }

        // Handle confirmation
        const handleConfirm = function() {
            // Submit the form
            bulkForm.submit();
            modal.hide();
        };

        // Remove previous event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Add new event listener
        newConfirmBtn.addEventListener('click', handleConfirm);

        // Show modal
        modal.show();
    });

    // ===== UTILITY FUNCTIONS =====

    // Update header checkbox state based on row selections
    function updateHeaderCheckboxState() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked:not(:disabled)');
        const enabledBoxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
        const checkedCount = checkedBoxes.length;
        const totalCount = enabledBoxes.length;

        if (checkedCount === 0) {
            // No checkboxes selected
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            // All checkboxes selected
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            // Some checkboxes selected (indeterminate state)
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Update bulk action button enabled/disabled state
    function updateBulkActionButton() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        const hasAction = bulkActionSelect.value !== '';
        
        // Fix: Do not disable the button. Let validation handle it. 
        // WP allows clicking Apply even if nothing is selected (it just warns).
        // bulkApplyBtn.disabled = !(checkedCount > 0 && hasAction); 
        bulkApplyBtn.disabled = false;
    }

    // ===== ENHANCE SUCCESS MESSAGES =====
    // Make success messages more specific and action-oriented
    function enhanceSuccessMessages() {
        const successAlert = document.querySelector('.alert-success');
        if (!successAlert) return;

        const messageText = successAlert.textContent.trim();

        // Parse generic success messages and make them more specific
        // Example: "3 posts processed successfully." -> "3 posts published."

        // Look for patterns like "X posts processed successfully"
        const match = messageText.match(/(\d+)\s+posts?\s+processed\s+successfully/);
        if (match) {
            const postCount = parseInt(match[1]);
            const action = bulkActionSelect.value;

            let actionText = '';
            switch (action) {
                case 'publish':
                    actionText = postCount === 1 ? 'published' : 'published';
                    break;
                case 'draft':
                    actionText = postCount === 1 ? 'moved to draft' : 'moved to draft';
                    break;
                case 'delete':
                    actionText = postCount === 1 ? 'moved to trash' : 'moved to trash';
                    break;
                default:
                    actionText = 'processed';
            }

            const newMessage = `${postCount} post${postCount > 1 ? 's' : ''} ${actionText}.`;
            successAlert.textContent = newMessage;
        }
    }

    // ===== INITIALIZATION =====
    updateHeaderCheckboxState();
    updateBulkActionButton();

    // Enhance any existing success messages on page load
    enhanceSuccessMessages();
});
</script>
@endpush
