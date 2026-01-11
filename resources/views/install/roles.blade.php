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

            <!-- Roles Setup Info -->
            <div class="alert alert-info text-center mb-4">
                <i class="fas fa-users fa-2x mb-3 text-primary"></i>
                <h5>Setting up Roles and Permissions</h5>
                <p class="mb-0">The system will create default roles and permissions for user management.</p>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-crown fa-2x mb-2 text-warning"></i>
                            <h6>Administrator</h6>
                            <p class="small text-muted">Full system access</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-edit fa-2x mb-2 text-info"></i>
                            <h6>Editor</h6>
                            <p class="small text-muted">Content management access</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('install.step', $prevStep) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </a>

                <form action="{{ route('install.process', $currentStep) }}" method="POST" id="roles-form">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="next-btn">
                        <i class="fas fa-arrow-right me-2"></i>Next
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('roles-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('next-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Setting up roles...';

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
            btn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Next';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Next';
    });
});
</script>
@endpush
@endsection
