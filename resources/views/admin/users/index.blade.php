@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Users</h4>
</div>

{{-- ===== ALERTS ===== --}}
@if(session('success'))
<div class="alert alert-success py-2">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger py-2">
    {{ $errors->first() }}
</div>
@endif

{{-- ===== USERS TABLE ===== --}}
<div class="table-responsive">
    <table class="table table-sm table-hover align-middle shadow-sm rounded">
        <thead class="table-light text-muted small">
            <tr>
                <th width="50">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-muted">{{ $user->id }}</td>
                <td class="fw-medium">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-inline">
                        @csrf
                        <select name="role_slug" class="form-select form-select-sm d-inline-block w-auto"
                                onchange="if(confirm('Are you sure you want to change the role for {{ addslashes($user->name) }}?')) { this.form.submit(); } else { this.value = '{{ $user->roles->first()?->slug ?? \'\' }}'; }">
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
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($users->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
    </div>
    <div>{{ $users->links() }}</div>
</div>
@endif

@endsection
