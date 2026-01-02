@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Post Details</h4>
        <div>
            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-warning btn-sm me-2">
                Edit Post
            </a>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Back to Posts
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- POST CONTENT --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $post->title }}</h5>
                </div>
                <div class="card-body">
                    @if($post->thumbnail)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                             class="img-fluid rounded"
                             alt="{{ $post->title }}">
                    </div>
                    @endif

                    <div class="mb-3">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    @if($post->tags->count() > 0)
                    <div>
                        <strong>Tags:</strong>
                        @foreach($post->tags as $tag)
                        <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- POST INFO --}}
            <div class="card">
                <div class="card-header">
                    <strong>Post Information</strong>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($post->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Category:</dt>
                        <dd class="col-sm-8">{{ $post->primaryCategory()?->name ?? 'Uncategorized' }}</dd>

                        <dt class="col-sm-4">Slug:</dt>
                        <dd class="col-sm-8"><code>{{ $post->slug }}</code></dd>

                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">{{ $post->created_at->format('M j, Y H:i') }}</dd>

                        <dt class="col-sm-4">Updated:</dt>
                        <dd class="col-sm-8">{{ $post->updated_at->format('M j, Y H:i') }}</dd>

                        @if($post->seo_title)
                        <dt class="col-sm-4">SEO Title:</dt>
                        <dd class="col-sm-8">{{ $post->seo_title }}</dd>
                        @endif

                        @if($post->seo_description)
                        <dt class="col-sm-4">SEO Desc:</dt>
                        <dd class="col-sm-8">{{ Str::limit($post->seo_description, 100) }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
