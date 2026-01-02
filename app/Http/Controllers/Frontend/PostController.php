<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of published posts (homepage).
     */
    public function index()
    {
        $posts = Post::with('category', 'tags')
            ->where('status', 'published')
            ->latest()
            ->paginate(6);

        return view('frontend.posts.index', compact('posts'));
    }

    /**
     * Display the specified published post.
     */
    public function show($slug)
    {
        $post = Post::with('category', 'tags')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $seoTitle = $post->seo_title ?: $post->title;
        $seoDescription = $post->seo_description ?: Str::limit(strip_tags($post->content), 160);

        $relatedPosts = Post::with('category')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.posts.show', compact('post', 'seoTitle', 'seoDescription', 'relatedPosts'));
    }
}
