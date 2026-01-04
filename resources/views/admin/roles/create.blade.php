@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ===== HEADER ===== --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Create New Role</h4>
                    <small class="text-muted">Define a custom role with specific permissions</small>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    ← Back to Roles
                </a>
            </div>

            {{-- ===== FORM ===== --}}
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf

                        {{-- ===== BASIC INFO ===== --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Display name for the role</small>
                            </div>

                            <div class="col-md-6">
                                <label for="slug" class="form-label">Role Slug</label>
                                <input type="text"
                                       class="form-control @error('slug') is-invalid @enderror"
                                       id="slug"
                                       name="slug"
                                       value="{{ old('slug') }}"
                                       pattern="[a-z0-9_-]+"
                                       required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique identifier (lowercase, no spaces)</small>
                            </div>
                        </div>

                        {{-- ===== PERMISSIONS ===== --}}
                        <div class="mb-4">
                            <h5 class="mb-3">Permissions</h5>
                            <p class="text-muted small mb-4">
                                Select the permissions this role should have. Only whitelisted permissions are available.
                            </p>

                            @error('permissions')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            @foreach($permissions as $module => $modulePermissions)
                                <div class="card mb-4 border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-capitalize fw-semibold">
                                            <i class="fas fa-folder me-2 text-muted"></i>{{ ucfirst($module) }}
                                            <small class="text-muted ms-2">({{ count($modulePermissions) }} permissions)</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @foreach($modulePermissions as $slug => $permissionData)
                                                <div class="col-xl-4 col-lg-6 col-md-6">
                                                    <div class="form-check permission-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $slug }}"
                                                               id="perm-{{ $slug }}"
                                                               {{ in_array($slug, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label d-block" for="perm-{{ $slug }}">
                                                            <span class="fw-semibold text-dark">{{ $permissionData['label'] }}</span>
                                                            <br>
                                                            <small class="text-muted">{{ $permissionData['description'] }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ===== ACTIONS ===== --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
