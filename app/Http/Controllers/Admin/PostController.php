<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('category', 'tags')->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5',
            'content' => 'required',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('posts', 'public');
        }

        $post = Post::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'status' => $request->status,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'category_id' => $request->category_id,
        ]);

        $this->syncTags($post, $request->tags);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('tags');
        $seoTitle = $post->seo_title ?: $post->title;
        $seoDescription = $post->seo_description ?: Str::limit(strip_tags($post->content), 160);
        return view('admin.posts.show', compact('post', 'seoTitle', 'seoDescription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|min:5',
            'content' => 'required',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        if ($slug !== $post->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $thumbnailPath = $post->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('posts', 'public');
        }

        $post->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'status' => $request->status,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'category_id' => $request->category_id,
        ]);

        $this->syncTags($post, $request->tags);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    private function syncTags(Post $post, $tagsString)
    {
        if (!$tagsString) {
            $post->tags()->detach();
            return;
        }

        $tagNames = array_map('trim', explode(',', $tagsString));
        $tagNames = array_filter($tagNames, function($tag) {
            return !empty($tag);
        });

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tagName = strtolower($tagName);
            $slug = Str::slug($tagName);
            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $tagName, 'slug' => $slug]
            );
            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
    }
}
