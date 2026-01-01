<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostPublicController extends Controller
{
    public function show($slug)
    {
        $post = Post::with('category', 'tags')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $seoTitle = $post->seo_title ?: $post->title;
        $seoDescription = $post->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 160);

        $relatedPosts = \App\Models\Post::with('category')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();

        return view('posts.show', compact('post', 'seoTitle', 'seoDescription', 'relatedPosts'));
    }
}
