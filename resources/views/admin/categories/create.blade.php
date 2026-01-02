@extends('layouts.admin')

@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf

<div class="container-fluid">
    <div class="row g-4">

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="col-lg-8">

            {{-- ALERTS --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- NAME --}}
            <div class="mb-3">
                <input type="text"
                       class="form-control form-control-lg border-0 shadow-none"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       placeholder="Add category name"
                       style="font-size:2rem;font-weight:600;border-bottom:2px solid #dee2e6"
                       required>
            </div>

            {{-- SLUG PREVIEW --}}
            <div class="mb-4">
                <small class="text-muted">Permalink: /category/</small>
                <input type="text"
                       class="border-0 bg-transparent p-0 text-muted small"
                       id="slug-preview"
                       value="{{ old('name') ? Str::slug(old('name')) : '' }}"
                       readonly>
            </div>

            {{-- DESCRIPTION --}}
            <textarea name="description"
                      id="description"
                      rows="8"
                      class="form-control border-0 shadow-none"
                      placeholder="Add category description...">{{ old('description') }}</textarea>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-lg-4">

            {{-- PUBLISH --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Publish</div>
                <div class="card-body">

                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm mb-3" name="status" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button class="btn btn-primary w-100 mb-2">Create Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        ← Back to Categories
                    </a>
                </div>
            </div>

            {{-- PARENT CATEGORY --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Parent Category</div>
                <div class="card-body">
                    <label class="form-label small">Select Parent (Optional)</label>
                    <select class="form-select form-select-sm" name="parent_id">
                        <option value="">No Parent (Top Level)</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Maximum 1 level deep.</small>
                </div>
            </div>

            {{-- SLUG --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Category Slug</div>
                <div class="card-body">
                    <label class="form-label small">URL Slug</label>
                    <input type="text"
                           class="form-control form-control-sm"
                           name="slug"
                           id="slug"
                           value="{{ old('slug') }}"
                           placeholder="auto-generated">
                    <small class="text-muted">Leave empty to auto-generate from name.</small>
                </div>
            </div>

        </div>
    </div>
</div>
</form>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-'); // Replace multiple hyphens with single
    document.getElementById('slug').value = slug;
    document.getElementById('slug-preview').value = slug;
});
</script>
@endsection
