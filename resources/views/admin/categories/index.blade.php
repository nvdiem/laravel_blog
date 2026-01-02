@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Categories</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
        + Create Category
    </a>
</div>

{{-- ===== STATUS TABS ===== --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}"
           href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => ''])) }}">
            All <span class="badge bg-secondary">{{ $parentCategories->count() + $childCategories->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'active' ? 'active' : '' }}"
           href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => 'active'])) }}">
            Active <span class="badge bg-success">{{ $parentCategories->where('status', 'active')->count() + $childCategories->where('status', 'active')->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'inactive' ? 'active' : '' }}"
           href="{{ route('admin.categories.index', array_merge(request()->query(), ['status' => 'inactive'])) }}">
            Inactive <span class="badge bg-secondary">{{ $parentCategories->where('status', 'inactive')->count() + $childCategories->where('status', 'inactive')->count() }}</span>
        </a>
    </li>
</ul>

{{-- ===== FILTERS ===== --}}
<form method="GET" id="filter-form" class="row g-3 mb-3">
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search categories..."
               value="{{ request('search') }}"
               style="height: 30px; font-size: 13px; border: 1px solid #d1d5db; border-radius: 4px; background-color: #f9fafb; color: #374151;">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="parent_id" class="form-select form-select-sm">
            <option value="">All Parents</option>
            <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>Top Level Only</option>
            @foreach($allParentCategories as $parent)
            <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                Child of {{ $parent->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
    </div>
    <div class="col-md-2 text-end">
        @if(request()->hasAny(['search', 'status', 'parent_id']))
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
    {{ session('error') }}
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
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-muted">
                        Name
                        @if(request('sort_by') === 'name')
                            {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </a>
                </th>
                <th>Slug</th>
                <th>Description</th>
                <th>Parent</th>
                <th width="90">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => request('sort_by') === 'status' && request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-muted">
                        Status
                        @if(request('sort_by') === 'status')
                            {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </a>
                </th>
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
                $allCategories = $allCategories->sortBy('name');
            @endphp

            @forelse($allCategories as $category)
            <tr class="post-row">
                <td class="text-muted">{{ $category->id }}</td>

                <td class="fw-medium">
                    @if($category->parent)
                        <span style="margin-left: 20px;">&mdash;</span>
                    @endif
                    {{ $category->name }}
                    <div class="row-actions" style="display: none;">
                        <a href="{{ route('admin.categories.edit', $category) }}">Edit</a> |
                        <a href="{{ route('admin.categories.show', $category) }}">View</a>
                        @if($category->post_count == 0 && (!$category->parent || $category->parent->post_count == 0))
                        | <a href="#" class="text-danger" onclick="if(confirm('Delete this category?')) { event.preventDefault(); document.getElementById('delete-form-{{ $category->id }}').submit(); }">Trash</a>
                        @endif
                    </div>
                </td>

                <td class="text-muted small">
                    {{ $category->slug }}
                </td>

                <td class="text-muted small">
                    {{ $category->description ? Str::limit($category->description, 40) : '—' }}
                </td>

                <td>
                    @if($category->parent)
                        <span class="badge bg-info-subtle text-info border border-info small">
                            {{ $category->parent->name }}
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border small">
                            Top Level
                        </span>
                    @endif
                </td>

                <td>
                    @if($category->status === 'active')
                        <span class="badge bg-success-subtle text-success border border-success">
                            Active
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border">
                            Inactive
                        </span>
                    @endif
                </td>

                <td class="text-center">
                    <span class="badge bg-primary">{{ $category->post_count }}</span>
                </td>

                <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    No categories found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($categories->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
    </div>
    <div>
        {{ $categories->links() }}
    </div>
</div>
@endif

@endsection
