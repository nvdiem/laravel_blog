@extends('layouts.frontend')


@section('hero')
<section class="hero">
    <div class="container-xl">
        <h1>Engineering Notes</h1>
        <p>Insights on Backend, System Design & AI.</p>
    </div>
</section>
@endsection

@section('content')
<div class="container-xl">

    {{-- ===== POSTS GRID ===== --}}
    <div class="row g-4 g-lg-5"> {{-- Wider gutter --}}
        @forelse($posts as $post)
            <div class="col-lg-4 col-md-6">
                 
                {{-- Make the whole card clickable link wrapper --}}
                <div class="post-card group position-relative">
                    
                    {{-- THUMBNAIL --}}
                    <a href="{{ route('posts.show', $post->slug) }}" class="post-thumbnail d-block position-relative">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}">
                        @else
                             {{-- Generative colored placeholder --}}
                            <div style="width:100%; height:100%; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                                <i class="fas fa-pen-nib fs-2 opacity-50"></i>
                            </div>
                        @endif

                        @if(in_array($post->id, $popularPostIds ?? []))
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger text-white shadow-sm border border-white">
                                <i class="fas fa-fire me-1"></i> Popular
                            </span>
                        @endif
                    </a>

                    {{-- META --}}
                    <div class="post-meta d-flex gap-2 align-items-center">
                        @if($post->primaryCategory && $post->primaryCategory->first())
                            <span class="text-primary">{{ $post->primaryCategory->first()->name }}</span>
                        @else
                            <span>Uncategorized</span>
                        @endif
                        <span class="text-muted">•</span>
                        <span>{{ $post->created_at?->format('M j, Y') }}</span>
                    </div>

                    {{-- TITLE --}}
                    <h2 class="post-title">
                        <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none stretched-link">
                            {{ $post->title }}
                        </a>
                    </h2>

                    {{-- EXCERPT --}}
                    <p class="post-excerpt">
                         {{ Str::limit(strip_tags($post->content), 120) }}
                    </p>

                    {{-- TAGS (Optional, keeping it clean for now, maybe hide on grid) --}}
                    {{-- 
                    <div class="post-tags mt-auto pt-2">
                        @foreach($post->tags->take(2) as $tag)
                            <span class="badge bg-light text-secondary border fw-normal">{{ $tag->name }}</span>
                        @endforeach
                    </div> 
                    --}}

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-light rounded-3">
                    <h3 class="text-muted">No posts found</h3>
                    <p class="text-muted">Check back later for new content.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ===== PAGINATION ===== --}}
    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-5 pt-4 border-top">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
