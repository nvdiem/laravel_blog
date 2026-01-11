@extends('layouts.install')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="install-card bg-white p-5 text-center">
            <!-- Success Icon -->
            <div class="mb-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h1 class="h2 mb-2">Installation Complete!</h1>
                <p class="text-muted">Your Laravel CMS has been successfully installed and configured.</p>
            </div>

            <!-- Installation Summary -->
            <div class="alert alert-success mb-4">
                <h5 class="alert-heading mb-2">What was installed:</h5>
                <ul class="text-start mb-0">
                    <li>Database tables and relationships</li>
                    <li>Administrator account with full access</li>
                    <li>User roles and permissions system</li>
                    <li>Content directory structure</li>
                    <li>Basic site configuration</li>
                </ul>
            </div>

            <!-- Next Steps -->
            <div class="mb-4">
                <h5>Next Steps:</h5>
                <div class="row text-start">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-sign-in-alt text-primary me-2"></i>
                                    Login to Admin
                                </h6>
                                <p class="card-text small">Access the admin panel to manage your content.</p>
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Go to Login</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-home text-success me-2"></i>
                                    Visit Your Site
                                </h6>
                                <p class="card-text small">Check out your new blog homepage.</p>
                                <a href="{{ url('/') }}" class="btn btn-success btn-sm" target="_blank">View Site</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-warning">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-shield-alt me-2"></i>
                    Important Security Steps
                </h6>
                <ul class="text-start mb-0 small">
                    <li>Change the default administrator password</li>
                    <li>Configure proper file permissions</li>
                    <li>Set up regular backups</li>
                    <li>Keep your system updated</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
