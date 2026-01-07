@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                <h1 class="display-5 fw-bold text-muted">Coming Soon</h1>
                <h2 class="h4 mb-4">{{ $page->title }}</h2>
                <p class="lead text-muted mb-4">
                    This page is currently under construction and will be available soon.
                </p>
                <div class="mb-4">
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title">Page Information</h5>
                    <div class="row text-start">
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Page:</strong> {{ $page->title }}</p>
                            <p class="mb-2"><strong>Status:</strong>
                                <span class="badge bg-success">Published</span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Created:</strong> {{ $page->created_at->format('M j, Y') }}</p>
                            <p class="mb-2"><strong>Last Updated:</strong> {{ $page->updated_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
