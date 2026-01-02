@extends('layouts.frontend')

@section('title', $seoTitle)
@section('description', $seoDescription)

@section('content')
<div class="container-xl">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Category: {{ $category->name }}
            </li>
        </ol>
    </nav>

    {{-- Category Header --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 fw-bold text-dark mb-3">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="lead text-muted mb-0">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Posts Grid --}}
    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-lg-4 col-md-6">
                <article class="card h-100">

                    {{-- THUMBNAIL --}}
                    @if($post->thumbnail)
                        <img
                            src="{{ asset('storage/' . $post->thumbnail) }}"
                            class="card-img-top"
                            alt="{{ $post->title }}"
                        >
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center"
                             style="height:180px; background: linear-gradient(135deg, #667eea, #764ba2);">
                            <span class="text-white fs-1 opacity-75"></></span>
                        </div>
                    @endif

                    {{-- BODY --}}
                    <div class="card-body d-flex flex-column">

                        {{-- TITLE --}}
                        <h2 class="card-title mb-2">
                            <a href="{{ route('posts.show', $post->slug) }}"
                               class="text-decoration-none text-dark">
                                {{ $post->title }}
                            </a>
                        </h2>

                        {{-- EXCERPT --}}
                        <p class="text-muted small flex-grow-1 mb-3">
                            {{ Str::limit(strip_tags($post->content), 140) }}
                        </p>

                        {{-- TAGS --}}
                        <div class="mb-3">
                            @forelse($post->tags as $tag)
                                <span class="badge bg-secondary-subtle text-secondary border me-1 small">
                                    {{ $tag->name }}
                                </span>
                            @empty
                            @endforelse
                        </div>

                        {{-- META + CTA --}}
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <small class="text-muted">
                                {{ $post->created_at->format('M j, Y') }}
                            </small>
                            <a href="{{ route('posts.show', $post->slug) }}"
                               class="btn btn-outline-primary btn-sm">
                                Read article →
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">No posts found in this category</h4>
                <p class="text-muted">New articles in "{{ $category->name }}" will appear here.</p>
                <a href="{{ url('/') }}" class="btn btn-primary">← Back to All Posts</a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
