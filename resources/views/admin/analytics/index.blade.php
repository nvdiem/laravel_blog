@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid px-3">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Analytics</h4>
            <small class="text-muted">Blog performance insights and top content</small>
        </div>
        <div class="text-muted small">
            Data updated in real-time
        </div>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="row g-3 mb-3">
        {{-- TOTAL VIEWS --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center py-3">
                    <div class="display-4 fw-bold text-primary mb-2">{{ number_format($totalViews) }}</div>
                    <div class="text-muted">Total Views</div>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>

        {{-- 7 DAY VIEWS --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center py-3">
                    <div class="display-4 fw-bold text-success mb-2">{{ number_format($viewsLast7Days) }}</div>
                    <div class="text-muted">Views (7 days)</div>
                    <div class="mt-2">
                        @if($trend7Days === 'up')
                            <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>Trending Up</span>
                        @elseif($trend7Days === 'down')
                            <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>Trending Down</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>Stable</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 30 DAY VIEWS --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center py-3">
                    <div class="display-4 fw-bold text-info mb-2">{{ number_format($viewsLast30Days) }}</div>
                    <div class="text-muted">Views (30 days)</div>
                    <div class="mt-2">
                        @if($trend30Days === 'up')
                            <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>Trending Up</span>
                        @elseif($trend30Days === 'down')
                            <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>Trending Down</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>Stable</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TOP POSTS TABLES ===== --}}
    <div class="row g-3">
        {{-- TOP POSTS - LAST 7 DAYS --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-success"></i>Top Posts (Last 7 Days)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($topPosts7Days->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Post Title</th>
                                        <th class="border-0 text-end">Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPosts7Days as $index => $post)
                                        <tr>
                                            <td class="border-0">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-3">{{ $index + 1 }}</span>
                                                    <div>
                                                        <a href="{{ route('posts.show', $post->slug) }}"
                                                           target="_blank"
                                                           class="text-decoration-none fw-semibold">
                                                            {{ Str::limit($post->title, 50) }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $post->created_at->format('M j, Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 text-end">
                                                <span class="badge bg-success fs-6">{{ number_format($post->views_count) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-line fa-2x mb-3 opacity-50"></i>
                            <p>No views recorded in the last 7 days</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- TOP POSTS - LAST 30 DAYS --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-info"></i>Top Posts (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($topPosts30Days->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Post Title</th>
                                        <th class="border-0 text-end">Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPosts30Days as $index => $post)
                                        <tr>
                                            <td class="border-0">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-3">{{ $index + 1 }}</span>
                                                    <div>
                                                        <a href="{{ route('posts.show', $post->slug) }}"
                                                           target="_blank"
                                                           class="text-decoration-none fw-semibold">
                                                            {{ Str::limit($post->title, 50) }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $post->created_at->format('M j, Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 text-end">
                                                <span class="badge bg-info fs-6">{{ number_format($post->views_count) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-3 opacity-50"></i>
                            <p>No views recorded in the last 30 days</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== INFO BOX ===== --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-info-circle me-2"></i>Analytics Information
                </h6>
                <ul class="mb-0 small">
                    <li><strong>Data Source:</strong> Individual page views are tracked when posts are accessed</li>
                    <li><strong>Trend Calculation:</strong> Compares current period vs previous period (5% threshold)</li>
                    <li><strong>Real-time:</strong> Statistics update immediately when posts are viewed</li>
                    <li><strong>Performance:</strong> Minimal database impact with optimized aggregate queries</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.display-4 {
    font-size: 2.5rem;
}

.card {
    transition: transform .15s ease, box-shadow .15s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.1);
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 0.75rem;
}

.alert-info {
    border-left: 4px solid #0dcaf0;
    padding: 0.75rem 1rem;
}
</style>
@endpush
