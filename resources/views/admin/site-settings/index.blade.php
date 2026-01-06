@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="container-fluid px-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Site Settings</h4>
            <small class="text-muted">Configure site-wide branding and SEO settings</small>
        </div>
    </div>

    {{-- ===== ALERTS ===== --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ===== SETTINGS FORM ===== --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>General Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- ===== SITE NAME ===== --}}
                        <div class="mb-3">
                            <label for="site_name" class="form-label fw-semibold">Site Name</label>
                            <input type="text"
                                   class="form-control @error('site_name') is-invalid @enderror"
                                   id="site_name"
                                   name="site_name"
                                   value="{{ old('site_name', $settings['site_name']) }}"
                                   required>
                            <small class="form-text text-muted">The name of your site as displayed in the header</small>
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== SITE LOGO ===== --}}
                        <div class="mb-3">
                            <label for="site_logo" class="form-label fw-semibold">Site Logo</label>
                            <input type="file"
                                   class="form-control @error('site_logo') is-invalid @enderror"
                                   id="site_logo"
                                   name="site_logo"
                                   accept="image/*">
                            @if($settings['site_logo'])
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}"
                                         alt="Current logo"
                                         class="img-thumbnail"
                                         style="max-width: 200px; max-height: 100px;">
                                    <small class="text-muted d-block mt-1">Current logo preview</small>
                                </div>
                            @endif
                            <small class="form-text text-muted">Upload a logo image (JPEG, PNG, GIF, SVG - max 2MB)</small>
                            @error('site_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== PRIMARY COLOR ===== --}}
                        <div class="mb-3">
                            <label for="primary_color" class="form-label fw-semibold">Primary Color</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="color"
                                       class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                       id="primary_color"
                                       name="primary_color"
                                       value="{{ old('primary_color', $settings['primary_color']) }}"
                                       style="width: 60px; height: 38px;"
                                       required>
                                <input type="text"
                                       class="form-control @error('primary_color') is-invalid @enderror"
                                       id="primary_color_text"
                                       value="{{ old('primary_color', $settings['primary_color']) }}"
                                       readonly
                                       style="max-width: 120px;">
                            </div>
                            <small class="form-text text-muted">Choose the primary brand color for your site</small>
                            @error('primary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== SEO TITLE ===== --}}
                        <div class="mb-3">
                            <label for="seo_title" class="form-label fw-semibold">SEO Title</label>
                            <input type="text"
                                   class="form-control @error('seo_title') is-invalid @enderror"
                                   id="seo_title"
                                   name="seo_title"
                                   value="{{ old('seo_title', $settings['seo_title']) }}"
                                   maxlength="255">
                            <small class="form-text text-muted">Default page title for SEO (leave empty to use site name)</small>
                            @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== SEO DESCRIPTION ===== --}}
                        <div class="mb-4">
                            <label for="seo_description" class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control @error('seo_description') is-invalid @enderror"
                                      id="seo_description"
                                      name="seo_description"
                                      rows="3"
                                      maxlength="500">{{ old('seo_description', $settings['seo_description']) }}</textarea>
                            <small class="form-text text-muted">Default meta description for SEO (leave empty for automatic)</small>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== SUBMIT ===== --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== SIDEBAR ===== --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Settings Info
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        These settings control the global branding and SEO defaults for your site.
                    </p>

                    <h6 class="fw-semibold mb-2">What gets updated:</h6>
                    <ul class="small mb-0">
                        <li>Site logo in header</li>
                        <li>Primary brand colors</li>
                        <li>Default page titles</li>
                        <li>Meta descriptions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.form-control-color {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>
@endpush

@push('scripts')
<script>
// Sync color picker with text input
document.getElementById('primary_color').addEventListener('input', function() {
    document.getElementById('primary_color_text').value = this.value;
});

// Initialize text input with current color
document.getElementById('primary_color_text').value = document.getElementById('primary_color').value;
</script>
@endpush