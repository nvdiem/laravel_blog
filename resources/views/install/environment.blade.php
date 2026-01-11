@extends('layouts.install')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
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

            <!-- Environment Checks -->
            <div class="mb-4">
                <h5 class="mb-3">System Requirements Check</h5>

                @if($checks['passed'])
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        All system requirements are met! You can proceed to the next step.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Some requirements are not met. Please fix the issues below before proceeding.
                    </div>
                @endif

                <div class="row">
                    @foreach($checks['checks'] as $key => $check)
                        <div class="col-md-6 mb-3">
                            <div class="check-item {{ $check['status'] ? 'check-passed' : 'check-failed' }}">
                                <div class="check-status">
                                    @if($check['status'])
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $check['name'] }}</strong>
                                    @if(isset($check['current']))
                                        <br><small class="text-muted">Current: {{ $check['current'] }}</small>
                                    @endif
                                    @if(!$check['status'])
                                        <br><small class="text-danger">{{ $check['message'] }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" disabled>
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </button>

                <form action="{{ route('install.process', $currentStep) }}" method="POST" id="environment-form">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ !$checks['passed'] ? 'disabled' : '' }} id="next-btn">
                        <i class="fas fa-arrow-right me-2"></i>Next
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('environment-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('next-btn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

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
</script>
@endpush
@endsection
