@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
        <i class="fas fa-plus fa-xs"></i> Add New Category
    </a>
</div>

{{-- ===== STATUS LINKS (ROW 1) ===== --}}
<div class="wp-status-filters mb-2">
    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => ''])) }}" 
       class="{{ !request('status') ? 'current' : '' }}">
        All <span class="count">({{ $parentCategories->count() + $childCategories->count() }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>
    
    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => 'active'])) }}" 
       class="{{ request('status') === 'active' ? 'current' : '' }}">
        Active <span class="count">({{ $parentCategories->where('status', 'active')->count() + $childCategories->where('status', 'active')->count() }})</span>
    </a>
    <span class="text-secondary opacity-25">|</span>

    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => 'inactive'])) }}" 
       class="{{ request('status') === 'inactive' ? 'current' : '' }}">
        Inactive <span class="count">({{ $parentCategories->where('status', 'inactive')->count() + $childCategories->where('status', 'inactive')->count() }})</span>
    </a>
</div>

{{-- ===== TOOLBAR (FILTERS + SEARCH) (ROW 2) ===== --}}
<div class="bulk-actions-container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2 flex-wrap">
        {{-- Bulk Actions Placeholder --}}
        <select class="form-select form-select-sm w-auto" disabled>
            <option>Bulk Actions</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm" disabled>Apply</button>

        {{-- Filters --}}
        <form method="GET" id="filter-form" class="d-flex gap-2 align-items-center mb-0 ms-2">
            <select name="parent_id" class="form-select form-select-sm w-auto">
                <option value="">All Parents</option>
                <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>Top Level Only</option>
                @foreach($allParentCategories as $parent)
                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                    Child of {{ $parent->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
            
            @if(request()->hasAny(['search', 'status', 'parent_id']))
                <a href="{{ route('admin.categories.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 ms-1 text-danger">
                    Clear
                </a>
            @endif
        </form>

        {{-- Search --}}
        <form method="GET" class="d-flex gap-1 align-items-center mb-0 ms-2" style="max-width: 200px;">
            <input type="text" name="search" class="form-control form-control-sm" 
                   placeholder="Search categories..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
        </form>
    </div>
    
    {{-- Summary (Right) --}}
    <div class="text-muted small">
        {{ $parentCategories->count() + $childCategories->count() }} items
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

@if(session('error'))
<div class="alert alert-danger d-flex align-items-center small mb-3">
    <i class="fas fa-exclamation-circle me-2"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger m-2 py-1 px-3 small">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ===== TABLE ===== --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th width="30" class="text-center">
                    <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                </th>
                <th>Name</th>
                <th>Description</th>
                <th>Slug</th>
                <th width="80">Posts</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allCategories = collect();
                foreach($parentCategories as $parent) {
                    $allCategories->push($parent);
                    foreach($parent->children as $child) {
                        $allCategories->push($child);
                    }
                }
                // If searching, we might lose hierarchy structure in pure collection, 
                // but for now let's rely on the controller's data passing.
                // Re-sorting might break the parent-child visual order if not careful.
                // The original code sorted by name which breaks hierarchy grouping.
                // Let's assume the controller passed them reasonably or just respect existing loop.
            @endphp

            @forelse($allCategories as $category)
            <tr class="post-row">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input row-checkbox" name="category_ids[]" value="{{ $category->id }}">
                </td>
                <td class="position-relative">
                    <strong class="d-block mb-1 post-title">
                        @if($category->parent)
                            <span class="text-muted me-1">—</span>
                        @endif
                        <a href="{{ route('admin.categories.edit', $category) }}">{{ $category->name }}</a>
                    </strong>
                    <div class="row-actions small">
                        <span class="edit"><a href="{{ route('admin.categories.edit', $category) }}">Edit</a></span>
                         <span class="text-muted opacity-50">|</span> 
                        <span class="view"><a href="{{ route('admin.categories.show', $category) }}">View</a></span>
                        @if($category->post_count == 0 && (!$category->parent || $category->parent->post_count == 0))
                         <span class="text-muted opacity-50">|</span> 
                        <span class="trash"><a href="#" class="text-danger" onclick="if(confirm('Delete this category?')) { event.preventDefault(); document.getElementById('delete-form-{{ $category->id }}').submit(); }">Delete</a></span>
                        @endif
                        
                        <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
                <td>{{ $category->description ? Str::limit($category->description, 50) : '—' }}</td>
                <td>{{ $category->slug }}</td>
                <td class="text-center">
                    <span class="badge position-relative text-bg-light border text-dark">{{ $category->post_count }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    No categories found.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center"><input type="checkbox" class="form-check-input select-all-footer"></th>
                <th>Name</th>
                <th>Description</th>
                <th>Slug</th>
                <th>Posts</th>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($categories->hasPages())
<div class="mt-2 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
    </div>
    <div>
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Basic Checkbox Logic for Categories (Visual only as bulk actions not fully implemented on backend for cats maybe)
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    });
</script>
@endpush
