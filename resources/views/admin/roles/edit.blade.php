@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid px-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Edit Role: {{ $role->name }}</h4>
            <small class="text-muted">Modify role permissions and settings</small>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>

    {{-- ===== FORM ===== --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        {{-- ===== BASIC INFO ===== --}}
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <label for="name" class="form-label fw-semibold">Role Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $role->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Display name for the role</small>
                            </div>

                            <div class="col-lg-6">
                                <label for="slug" class="form-label fw-semibold">Role Slug</label>
                                <input type="text"
                                       class="form-control-plaintext bg-light"
                                       id="slug"
                                       value="{{ $role->slug }}"
                                       readonly>
                                <small class="form-text text-muted">Slug cannot be changed</small>
                            </div>
                        </div>

                        {{-- ===== PERMISSIONS ===== --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Permissions</h5>
                                <small class="text-muted">{{ $role->permissions->count() }} of {{ collect($permissions)->flatten()->count() }} selected</small>
                            </div>
                            <p class="text-muted small mb-4">
                                Modify the permissions for this role. Only whitelisted permissions are available.
                            </p>

                            @error('permissions')
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                </div>
                            @enderror

                            @foreach($permissions as $module => $modulePermissions)
                                <div class="card mb-4 border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-capitalize fw-semibold">
                                            <i class="fas fa-folder me-2 text-muted"></i>{{ $module }}
                                            <small class="text-muted ms-2">({{ count($modulePermissions) }} permissions)</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @foreach($modulePermissions as $slug => $description)
                                                <div class="col-xl-4 col-lg-6 col-md-6">
                                                    <div class="form-check permission-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $slug }}"
                                                               id="perm-{{ $slug }}"
                                                               {{ in_array($slug, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                        <label class="form-check-label d-block" for="perm-{{ $slug }}">
                                                            <span class="fw-medium text-dark">{{ $slug }}</span>
                                                            <br>
                                                            <small class="text-muted">{{ $description }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ===== ROLE INFO ===== --}}
                        <div class="alert alert-info border-0 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong class="text-dark">Created</strong><br>
                                    <small class="text-muted">{{ $role->created_at->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong class="text-dark">Last Updated</strong><br>
                                    <small class="text-muted">{{ $role->updated_at->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong class="text-dark">Users Assigned</strong><br>
                                    <small class="text-muted">{{ $role->users->count() }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong class="text-dark">Permissions</strong><br>
                                    <small class="text-muted">{{ $role->permissions->count() }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- ===== ACTIONS ===== --}}
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.permission-check {
    padding: 1rem;
    border-radius: 8px;
    transition: background-color 0.2s ease;
    line-height: 1.5;
}

.permission-check:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    cursor: pointer;
    margin-bottom: 0;
}

.card-body {
    padding: 2rem;
}

.card-header {
    padding: 1rem 2rem;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #0c63e4;
}

.btn {
    padding: 0.5rem 1.5rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    .card-body {
        padding: 1.5rem;
    }

    .col-xl-4 {
        flex: 0 0 auto;
        width: 100%;
    }
}
</style>
@endpush
