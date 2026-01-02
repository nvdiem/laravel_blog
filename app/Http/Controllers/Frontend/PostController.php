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
        $posts = Post::with('categories', 'tags')
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
        $post = Post::with('categories', 'tags')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $seoTitle = $post->seo_title ?: $post->title;
        $seoDescription = $post->seo_description ?: Str::limit(strip_tags($post->content), 160);

        // Get related posts from the same primary category
        $primaryCategory = $post->primaryCategory();
        $relatedPosts = collect();
        if ($primaryCategory) {
            $relatedPosts = Post::with('categories')
                ->whereHas('categories', function ($query) use ($primaryCategory) {
                    $query->where('categories.id', $primaryCategory->id);
                })
                ->where('id', '!=', $post->id)
                ->where('status', 'published')
                ->latest()
                ->limit(3)
                ->get();
        }

        return view('frontend.posts.show', compact('post', 'seoTitle', 'seoDescription', 'relatedPosts'));
    }
}
