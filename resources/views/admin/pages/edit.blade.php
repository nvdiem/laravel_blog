@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 fw-normal mb-0 font-monospace-system">Edit Page</h1>
</div>

<form action="{{ route('admin.pages.update', $page) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Title --}}
            <div class="mb-3">
                <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control post-title-input" id="title" name="title"
                       value="{{ old('title', $page->title) }}" placeholder="Enter page title" required>
            </div>

            {{-- Slug --}}
            <div class="mb-3">
                <label for="slug" class="form-label">Page Slug <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="slug" name="slug"
                       value="{{ old('slug', $page->slug) }}" placeholder="page-slug" required>
                <div class="form-text">Used in URL: /p/{slug}</div>
            </div>

            {{-- Public URL --}}
            <div class="mb-3">
                <label class="form-label">Public URL</label>
                <div class="input-group">
                    <span class="input-group-text">{{ url('/p') }}/</span>
                    <input type="text" class="form-control" value="{{ $page->slug }}" readonly>
                </div>
                @if($page->status === 'published')
                <div class="form-text">
                    <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="text-primary">
                        View page <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
                @endif
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
                            <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="disabled" {{ old('status', $page->status) == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>

                    {{-- Allow Index --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_index" name="allow_index" value="1"
                                   {{ old('allow_index', $page->allow_index) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_index">
                                Allow search engines to index this page
                            </label>
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="mb-3 pt-3 border-top">
                        <div class="small text-muted">
                            <div>Created: {{ $page->created_at->format('M j, Y g:i A') }}</div>
                            <div>Last updated: {{ $page->updated_at->format('M j, Y g:i A') }}</div>
                            @if($page->creator)
                            <div>By: {{ $page->creator->name }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Page</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            {{-- Delete Section --}}
            @if($page->status !== 'published')
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Danger Zone
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> Deleting this page will permanently remove it and cannot be undone.
                    </div>
                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                          onsubmit="return confirm('Are you absolutely sure you want to permanently delete this page? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i> Permanently Delete Page
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

@endsection
