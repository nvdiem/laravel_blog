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

            <!-- Database Setup Info -->
            <div class="alert alert-info text-center mb-4">
                <i class="fas fa-database fa-2x mb-3 text-primary"></i>
                <h5>Database Configuration</h5>
                <p class="mb-0">The system will now configure your database and run the necessary migrations.</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-container mb-4">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%" id="progress-bar"></div>
                </div>
                <p class="text-center mt-2 text-muted" id="progress-text">Preparing database setup...</p>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('install.step', $prevStep) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </a>

                <form action="{{ route('install.process', $currentStep) }}" method="POST" id="database-form">
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
document.getElementById('database-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('next-btn');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Setting up database...';

    // Simulate progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';

        const messages = [
            'Connecting to database...',
            'Running migrations...',
            'Setting up tables...',
            'Almost done...'
        ];
        progressText.textContent = messages[Math.floor(progress / 25)] || 'Processing...';
    }, 500);

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
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressText.textContent = 'Database setup complete!';

        if (data.success) {
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            alert('Error: ' + (data.error || 'Unknown error occurred'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Next';
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Next';
    });
});
</script>
@endpush
@endsection
