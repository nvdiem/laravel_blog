@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Users</h1>
    {{-- Add User Button (Placeholder if route exists, else hidden) --}}
    {{-- <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1">Add New User</a> --}}
</div>

{{-- ===== TOOLBAR ===== --}}
<div class="bulk-actions-container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
        {{-- Bulk Actions Placeholder --}}
        <select class="form-select form-select-sm w-auto" disabled>
            <option>Bulk Actions</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm" disabled>Apply</button>
        
        {{-- Role Filter (Placeholder) --}}
        <select class="form-select form-select-sm w-auto" disabled>
            <option>Change role to...</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm" disabled>Change</button>
    </div>

    <div class="text-muted small">
        {{ $users->total() }} items
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
                <th width="50">ID</th>
                <th>Username</th> 
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="post-row">
                 <td class="text-center">
                    <input type="checkbox" class="form-check-input row-checkbox" name="user_ids[]" value="{{ $user->id }}">
                </td>
                <td class="text-muted">{{ $user->id }}</td>
                <td class="position-relative">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <strong class="post-title">
                            <a href="#">{{ $user->name }}</a>
                        </strong>
                    </div>
                    <div class="row-actions small" style="margin-left: 40px;">
                        <span class="edit"><a href="#">Edit</a></span>
                         <span class="text-muted opacity-50">|</span> 
                        <span class="view"><a href="#" class="text-danger">Delete</a></span>
                    </div>
                </td>
                <td>{{ $user->name }}</td>
                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                <td>
                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-inline role-form">
                        @csrf
                        <select name="role_slug" class="form-select form-select-sm w-auto border-0 bg-transparent fw-bold text-primary" 
                                style="box-shadow: none; cursor: pointer;"
                                onchange="if(confirm('Change role for {{ $user->name }}?')) { this.form.submit(); } else { this.value = '{{ $user->roles->first()?->slug }}'; }">
                            <option value="">No Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->slug }}"
                                    {{ $user->roles->first()?->slug === $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>
                    <span class="badge text-bg-light border text-dark">0</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center"><input type="checkbox" class="form-check-input select-all-footer"></th>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($users->hasPages())
<div class="mt-2 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
    </div>
    <div>{{ $users->links('pagination::bootstrap-5') }}</div>
</div>
@endif

@endsection

@push('scripts')
<script>
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
