@extends('layouts.frontend')

@section('content')
<div class="container-xl">

    {{-- ===== POSTS GRID ===== --}}
    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-lg-4 col-md-6">
                <article class="card h-100">

                    {{-- ===== THUMBNAIL ===== --}}
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

                    {{-- ===== BODY ===== --}}
                    <div class="card-body d-flex flex-column">

                        {{-- CATEGORY --}}
                        @if($post->category)
                            <span class="badge tech-badge mb-2 align-self-start">
                                {{ $post->category->name }}
                            </span>
                        @endif

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
                <h4 class="text-muted">No posts yet</h4>
                <p class="text-muted">New technical articles are coming soon.</p>
            </div>
        @endforelse
    </div>

    {{-- ===== PAGINATION ===== --}}
    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
