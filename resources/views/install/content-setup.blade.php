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

            <!-- Content Setup Info -->
            <div class="alert alert-info text-center mb-4">
                <i class="fas fa-folder fa-2x mb-3 text-primary"></i>
                <h5>Creating Content Directory Structure</h5>
                <p class="mb-0">Setting up directories for uploads, themes, plugins, and backups.</p>
            </div>

            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-upload fa-2x mb-2 text-success"></i>
                            <h6 class="card-title">Uploads</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-palette fa-2x mb-2 text-info"></i>
                            <h6 class="card-title">Themes</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-plug fa-2x mb-2 text-warning"></i>
                            <h6 class="card-title">Plugins</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-save fa-2x mb-2 text-danger"></i>
                            <h6 class="card-title">Backups</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('install.step', $prevStep) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </a>

                <form action="{{ route('install.process', $currentStep) }}" method="POST" id="content-form">
                    @csrf
                    <button type="submit" class="btn btn-success" id="complete-btn">
                        <i class="fas fa-check me-2"></i>Complete Installation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('content-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('complete-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Completing installation...';

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
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Installation';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Installation';
    });
});
</script>
@endpush
@endsection
