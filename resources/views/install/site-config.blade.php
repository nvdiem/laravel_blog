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

            <!-- Site Configuration Form -->
            <form action="{{ route('install.process', $currentStep) }}" method="POST" id="site-config-form">
                @csrf

                <div class="mb-3">
                    <label for="site_name" class="form-label">Site Name *</label>
                    <input type="text" class="form-control @error('site_name') is-invalid @enderror"
                           id="site_name" name="site_name" value="{{ old('site_name', 'Laravel Blog') }}" required>
                    @error('site_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="site_description" class="form-label">Site Description</label>
                    <textarea class="form-control @error('site_description') is-invalid @enderror"
                              id="site_description" name="site_description" rows="3">{{ old('site_description', 'A modern blog built with Laravel') }}</textarea>
                    @error('site_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
document.getElementById('site-config-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('next-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Configuring...';

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
