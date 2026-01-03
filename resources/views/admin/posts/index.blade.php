@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Posts</h4>
    @can('create', \App\Models\Post::class)
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
        + Create Post
    </a>
    @endcan
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
                    <th>Created</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th width="90">Thumbnail</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr class="post-row">
                    <td>
                        {{-- 🔧 FIX 2: checkbox nằm trong form --}}
                        <input type="checkbox"
                               class="form-check-input row-checkbox"
                               name="post_ids[]"
                               value="{{ $post->id }}">
                    </td>
                    <td class="text-muted">{{ $post->id }}</td>
                    <td class="fw-medium">
                        <div class="post-title-wrapper">
                            <strong class="post-title">{{ $post->title }}</strong>
                            <div class="row-actions">
                                <a href="{{ route('admin.posts.edit', $post) }}">Edit</a>
                                <a href="{{ route('admin.posts.show', $post) }}">View</a>
                                @if(auth()->user()->isAdmin())
                                <a href="#" class="text-danger" onclick="if(confirm('Delete this post?')) { event.preventDefault(); document.getElementById('delete-form-{{ $post->id }}').submit(); }">Trash</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $post->created_at->format('M j, Y') }}</td>
                    <td>{{ $post->primaryCategory->first()?->name ?? 'Uncategorized' }}</td>
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
                    <td colspan="8" class="text-center py-5">
                        @if(request('search'))
                            {{-- Search with no results --}}
                            <div class="empty-state">
                                <h6 class="text-muted mb-3">No posts found for your search</h6>
                                <p class="text-muted small mb-3">"{{ request('search') }}" returned no results.</p>
                                <a href="{{ route('admin.posts.index', array_diff_key(request()->query(), array_flip(['search']))) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    Clear Search
                                </a>
                            </div>
                        @elseif(request()->hasAny(['status', 'category_id', 'tag_id']))
                            {{-- Filtered with no results --}}
                            <div class="empty-state">
                                <h6 class="text-muted mb-3">No posts match your current filters</h6>
                                <p class="text-muted small mb-3">Try adjusting your filters or clearing them to see all posts.</p>
                                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Clear Filters
                                </a>
                            </div>
                        @else
                            {{-- No posts exist at all --}}
                            <div class="empty-state">
                                <h6 class="text-muted mb-3">No posts found</h6>
                                <p class="text-muted small mb-3">Get started by creating your first post.</p>
                                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
                                    Create Post
                                </a>
                            </div>
                        @endif
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

        // Enable button only if posts are selected AND an action is chosen
        bulkApplyBtn.disabled = !(checkedCount > 0 && hasAction);
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
