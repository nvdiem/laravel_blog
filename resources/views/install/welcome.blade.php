@extends('layouts.install')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="install-card bg-white p-5">
            <div class="text-center mb-4">
                <h2 class="h3 mb-3">Ready to Install?</h2>
                <p class="text-muted">
                    The installation process will set up your Laravel CMS with all necessary components.
                    This includes creating the database tables, setting up an admin user, and configuring
                    the basic site settings.
                </p>
            </div>

            <!-- Installation Steps Overview -->
            <div class="mb-4">
                <h5 class="mb-3">Installation Steps:</h5>
                <div class="step-indicator mb-4">
                    @foreach($steps as $stepNumber => $step)
                        <div class="step {{ $stepNumber == 1 ? 'active' : '' }}">
                            {{ $stepNumber }}
                        </div>
                        @if($stepNumber < count($steps))
                            <div class="step-line"></div>
                        @endif
                    @endforeach
                </div>

                <div class="row">
                    @foreach($steps as $stepNumber => $step)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                        {{ $stepNumber }}
                                    </div>
                                    <h6 class="card-title mb-1">{{ $step['name'] }}</h6>
                                    <p class="card-text small text-muted">{{ $step['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- System Requirements Notice -->
            <div class="alert alert-info">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-info-circle me-2"></i>
                    System Requirements
                </h6>
                <p class="mb-0 small">
                    Before proceeding, ensure your server meets the minimum requirements:
                    PHP 8.1+, MySQL/MariaDB, and proper file permissions.
                </p>
            </div>

            <!-- Start Installation Button -->
            <div class="text-center">
                <form action="{{ route('install.start') }}" method="POST" id="start-installation-form">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="start-btn">
                        <i class="fas fa-rocket me-2"></i>
                        Start Installation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('start-installation-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('start-btn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Starting Installation...';

    // Re-enable after 10 seconds in case of error
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }, 10000);
});
</script>
@endpush
@endsection
