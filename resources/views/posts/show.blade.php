@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            {{-- Back Link --}}
            <div class="mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none text-muted d-inline-flex align-items-center">
                    <svg width="20" height="20" fill="currentColor" class="me-2">
                        <path d="M15.5 7.5L10 13l-5.5-5.5L5 6l5 5 5-5z"/>
                    </svg>
                    ← Back to articles
                </a>
            </div>

            {{-- Article Header --}}
            <header class="mb-5">
                <h1 class="display-4 fw-bold text-dark mb-3">{{ $post->title }}</h1>

                <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-4">
                    <span class="d-flex align-items-center">
                        <svg width="16" height="16" fill="currentColor" class="me-1">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 1 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        {{ $post->created_at->format('F j, Y') }}
                    </span>

                    @if($post->category)
                    <span class="badge bg-primary">{{ $post->category->name }}</span>
                    @endif
                </div>
            </header>

            {{-- Thumbnail --}}
            @if($post->thumbnail)
            <div class="mb-5">
                <img
                    src="{{ asset('storage/' . $post->thumbnail) }}"
                    alt="{{ $post->title }}"
                    class="img-fluid rounded shadow-sm"
                    style="width: 100%; max-height: 400px; object-fit: cover;"
                >
            </div>
            @endif

            {{-- Article Content --}}
            <article class="blog-content mb-5">
                {!! $post->content !!}
            </article>

            {{-- Tags --}}
            @if($post->tags->count() > 0)
            <div class="mb-5">
                <h5 class="text-muted mb-3">Tags:</h5>
                <div>
                    @foreach($post->tags as $tag)
                    <span class="badge bg-secondary me-2 mb-2">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Related Articles --}}
            @if($relatedPosts->count() > 0)
            <div class="mb-5">
                <h4 class="mb-4 text-muted">Related Articles</h4>
                <div class="row g-3">
                    @foreach($relatedPosts as $relatedPost)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-2">
                                    <a href="{{ route('posts.public.show', $relatedPost->slug) }}"
                                       class="text-decoration-none text-dark fw-medium">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h6>
                                <p class="card-text small text-muted mb-2">
                                    {{ $relatedPost->created_at->format('M j, Y') }}
                                </p>
                                @if($relatedPost->category)
                                <span class="badge bg-primary-subtle text-primary small">
                                    {{ $relatedPost->category->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Article Footer --}}
            <hr class="my-5">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <small>Published on {{ $post->created_at->format('F j, Y \a\t g:i A') }}</small>
                </div>
                <a href="{{ url('/') }}" class="btn btn-outline-primary">← More Articles</a>
            </div>
        </div>
    </div>
</div>

<style>
.blog-content {
    max-width: 760px;
    margin: 0 auto;
    font-size: 18px;
    line-height: 1.8;
    color: #2c3e50;
}

.blog-content h1,
.blog-content h2,
.blog-content h3,
.blog-content h4,
.blog-content h5,
.blog-content h6 {
    margin-top: 2.5rem;
    margin-bottom: 1.2rem;
    font-weight: 600;
    color: #2d3748;
    line-height: 1.3;
}

.blog-content h1 { font-size: 2.2rem; }
.blog-content h2 { font-size: 1.8rem; }
.blog-content h3 { font-size: 1.5rem; }
.blog-content h4 { font-size: 1.3rem; }
.blog-content h5 { font-size: 1.1rem; }
.blog-content h6 { font-size: 1rem; }

.blog-content p {
    margin-bottom: 1.8rem;
    color: #4a5568;
}

.blog-content ul,
.blog-content ol {
    margin-bottom: 1.8rem;
    padding-left: 2rem;
}

.blog-content li {
    margin-bottom: 0.5rem;
}

.blog-content blockquote {
    border-left: 4px solid #e2e8f0;
    padding-left: 1.5rem;
    margin: 2.5rem 0;
    font-style: italic;
    color: #718096;
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-radius: 4px;
}

.blog-content pre {
    background: #1a202c;
    color: #e2e8f0;
    padding: 1.5rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2.5rem 0;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 0.95rem;
    line-height: 1.6;
}

.blog-content code {
    background: #edf2f7;
    color: #2d3748;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 0.9rem;
    font-weight: 500;
}

.blog-content pre code {
    background: transparent;
    color: inherit;
    padding: 0;
    font-size: inherit;
}

.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2.5rem 0;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.blog-content table {
    width: 100%;
    margin: 2.5rem 0;
    border-collapse: collapse;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.blog-content th,
.blog-content td {
    padding: 1rem;
    border: 1px solid #e2e8f0;
    text-align: left;
}

.blog-content th {
    background: #f7fafc;
    font-weight: 600;
    color: #2d3748;
}

.blog-content a {
    color: #3182ce;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s;
}

.blog-content a:hover {
    border-bottom-color: #3182ce;
}
</style>
@endsection
