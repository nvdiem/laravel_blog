@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold mb-0">Role Management</h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New Role
    </a>
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

{{-- ===== ROLES TABLE ===== --}}
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-shield-alt me-2"></i>Roles Overview
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="60">ID</th>
                        <th>Role Name</th>
                        <th>Identifier</th>
                        <th class="text-center" width="100">
                            <i class="fas fa-users text-muted me-1" title="Number of users with this role"></i>
                            Users
                        </th>
                        <th class="text-center" width="120">
                            <i class="fas fa-key text-muted me-1" title="Number of permissions assigned"></i>
                            Permissions
                        </th>
                        <th class="text-center" width="100">Type</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr class="role-row">
                        <td class="text-center text-muted small">{{ $role->id }}</td>
                        <td>
                            <strong class="text-dark">{{ $role->name }}</strong>
                        </td>
                        <td>
                            <code class="text-muted small">{{ $role->slug }}</code>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary fs-6 px-2 py-1">
                                <i class="fas fa-user me-1"></i>{{ $role->users_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6 px-2 py-1" title="{{ $role->permissions->count() }} permissions assigned">
                                <i class="fas fa-key me-1"></i>{{ $role->permissions->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(in_array($role->slug, config('permissions.system_roles')))
                                <span class="badge bg-danger fs-6 px-2 py-1" title="System roles cannot be modified">
                                    <i class="fas fa-lock me-1"></i>System
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6 px-2 py-1">
                                    <i class="fas fa-cog me-1"></i>Custom
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(!in_array($role->slug, config('permissions.system_roles')))
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit role name and permissions">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    @if($role->users_count === 0)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete the role \"{{ $role->name }}\"? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete this role">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small fw-medium">
                                    <i class="fas fa-shield-alt me-1"></i>System role (read-only)
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-shield-alt fa-3x mb-3 opacity-25"></i>
                            <h5 class="text-muted">No roles found</h5>
                            <p class="text-muted mb-0">Create your first custom role to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.role-row:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    border-bottom: 2px solid #dee2e6;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.alert-success {
    border-left: 4px solid #198754;
}

.alert-danger {
    border-left: 4px solid #dc3545;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

code {
    background-color: #f8f9fa;
    padding: 0.125rem 0.25rem;
    border-radius: 3px;
}
</style>
@endpush
