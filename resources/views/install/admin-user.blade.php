@extends('layouts.install')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="install-card bg-white p-5">
            <!-- Step Indicator -->
            <div class="step-indicator mb-4">
                @for($i = 1; $i <= count($steps); $i++)
                    <div class="step {{ $i < $currentStep ? 'completed' : ($i == $currentStep ? 'active' : '') }}">
                        @if($i < $currentStep)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    @if($i < count($steps))
                        <div class="step-line {{ $i < $currentStep ? 'completed' : '' }}"></div>
                    @endif
                @endfor
            </div>

            <!-- Step Content -->
            <div class="text-center mb-4">
                <h2 class="h3 mb-2">{{ $steps[$currentStep]['name'] }}</h2>
                <p class="text-muted">{{ $steps[$currentStep]['description'] }}</p>
            </div>

            <!-- Admin User Form -->
            <form action="{{ route('install.process', $currentStep) }}" method="POST" id="admin-user-form">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="admin_name" class="form-label">Administrator Name *</label>
                        <input type="text" class="form-control @error('admin_name') is-invalid @enderror"
                               id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                        @error('admin_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="admin_email" class="form-label">Administrator Email *</label>
                        <input type="email" class="form-control @error('admin_email') is-invalid @enderror"
                               id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                        @error('admin_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">This email will be used for system notifications.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="admin_password" class="form-label">Password *</label>
                        <input type="password" class="form-control @error('admin_password') is-invalid @enderror"
                               id="admin_password" name="admin_password" required>
                        @error('admin_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Minimum 8 characters required.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="admin_password_confirmation" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control"
                               id="admin_password_confirmation" name="admin_password_confirmation" required>
                        <div class="form-text">Re-enter your password to confirm.</div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-shield-alt me-2"></i>
                        Security Information
                    </h6>
                    <ul class="mb-0 small">
                        <li>Choose a strong password with at least 8 characters</li>
                        <li>Use a unique email address for this installation</li>
                        <li>Keep these credentials secure and don't share them</li>
                    </ul>
                </div>

                <!-- Navigation -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('install.step', $prevStep) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </a>

                    <button type="submit" class="btn btn-primary" id="next-btn">
                        <i class="fas fa-arrow-right me-2"></i>Next
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('admin-user-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('next-btn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Admin User...';

    // AJAX submission
    e.preventDefault();

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + (data.error || 'Unknown error occurred'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Password confirmation validation
document.getElementById('admin_password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('admin_password').value;
    const confirm = this.value;

    if (password !== confirm) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endpush
@endsection
