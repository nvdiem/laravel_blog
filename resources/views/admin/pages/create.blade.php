@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 fw-normal mb-0 font-monospace-system">Add New Page</h1>
</div>

<form action="{{ route('admin.pages.store') }}" method="POST">
    @csrf

    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Title --}}
            <div class="mb-3">
                <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control post-title-input" id="title" name="title"
                       value="{{ old('title') }}" placeholder="Enter page title" required>
            </div>

            {{-- Slug --}}
            <div class="mb-3">
                <label for="slug" class="form-label">Page Slug</label>
                <input type="text" class="form-control" id="slug" name="slug"
                       value="{{ old('slug') }}" placeholder="page-slug">
                <div class="form-text">Leave empty to auto-generate from title. Used in URL: /p/{slug}</div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Publish</div>
                <div class="card-body">
                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>

                    {{-- Allow Index --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_index" name="allow_index" value="1"
                                   {{ old('allow_index') ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_index">
                                Allow search engines to index this page
                            </label>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Page</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- JavaScript for auto-slug generation --}}
<script>
document.getElementById('title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        const slug = this.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        slugInput.value = slug;
    }
});
</script>

@endsection
