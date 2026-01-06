@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Roles</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
        <i class="fas fa-plus fa-xs"></i> Add New Role
    </a>
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="bulk-actions-container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
        {{-- Bulk Actions Placeholder --}}
        <select class="form-select form-select-sm w-auto" disabled>
            <option>Bulk Actions</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm" disabled>Apply</button>
    </div>

    <div class="text-muted small">
        {{ $roles->count() }} items
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

@if($errors->any())
<div class="alert alert-danger m-2 py-1 px-3 small">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li class="list-unstyled">{{ $error }}</li>
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
                <th>Role Name</th>
                <th>Identifier</th>
                <th width="100">Users</th>
                <th width="100">Permissions</th>
                <th width="100" class="text-center">Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            @php
                $isSystem = in_array($role->slug, config('permissions.system_roles') ?? []);
            @endphp
            <tr class="post-row">
                 <td class="text-center">
                    <input type="checkbox" class="form-check-input row-checkbox" name="role_ids[]" value="{{ $role->id }}" {{ $isSystem ? 'disabled' : '' }}>
                </td>
                <td class="position-relative">
                    <strong class="d-block mb-1 post-title">
                        <a href="{{ route('admin.roles.edit', $role) }}">{{ $role->name }}</a>
                        @if($isSystem)
                             <span class="text-muted small ms-1">— System</span>
                        @endif
                    </strong>
                    <div class="row-actions small">
                        @if(!$isSystem)
                            <span class="edit"><a href="{{ route('admin.roles.edit', $role) }}">Edit</a></span>
                            @if($role->users_count === 0)
                            <span class="text-muted opacity-50">|</span> 
                            <span class="trash"><a href="#" class="text-danger" onclick="if(confirm('Delete role {{ $role->name }}?')) { document.getElementById('delete-form-{{ $role->id }}').submit(); return false; }">Delete</a></span>
                            <form id="delete-form-{{ $role->id }}" action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-none">
                                @method('DELETE')
                                @csrf
                            </form>
                            @endif
                        @else
                            <span class="text-muted">System roles cannot be modified</span>
                        @endif
                    </div>
                </td>
                <td><code>{{ $role->slug }}</code></td>
                <td>
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                        <span class="badge position-relative text-bg-light border text-dark">{{ $role->users_count }}</span>
                    </a>
                </td>
                <td>
                    <span class="badge position-relative text-bg-light border text-dark">{{ $role->permissions->count() }}</span>
                </td>
                <td class="text-center">
                    @if($isSystem)
                        <i class="fas fa-lock text-muted" title="System Role"></i>
                    @else
                        <i class="fas fa-user-tag text-muted" title="Custom Role"></i>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    No roles found.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center"><input type="checkbox" class="form-check-input select-all-footer"></th>
                <th>Role Name</th>
                <th>Identifier</th>
                <th>Users</th>
                <th>Permissions</th>
                <th class="text-center">Type</th>
            </tr>
        </tfoot>
    </table>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');

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
