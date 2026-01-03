<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('categories', 'tags');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Tag filter
        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, ['title', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        $posts = $query->paginate(10);

        // Status counts for tabs
        $allCount = Post::count();
        $publishedCount = Post::where('status', 'published')->count();
        $draftCount = Post::where('status', 'draft')->count();

        // Filters data
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'allCount', 'publishedCount', 'draftCount', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:5',
            'content' => 'required',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'primary_category' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
        ]);

        // Validate primary category is in selected categories
        if ($request->primary_category && !in_array($request->primary_category, $request->categories ?? [])) {
            return back()->withErrors(['primary_category' => 'Primary category must be one of the selected categories.'])->withInput();
        }

        $this->postService->createPost($validated);

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
        $post->load('categories'); // Load categories relationship
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|min:5',
            'content' => 'required',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'primary_category' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
        ]);

        // Validate primary category is in selected categories
        if ($request->primary_category && !in_array($request->primary_category, $request->categories ?? [])) {
            return back()->withErrors(['primary_category' => 'Primary category must be one of the selected categories.'])->withInput();
        }

        $this->postService->updatePost($post, $validated);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }



    /**
     * Auto-save draft post (AJAX endpoint).
     */
    public function autosave(Request $request, Post $post)
    {
        // Only allow autosave for draft posts
        if ($post->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Auto-save only available for draft posts.'
            ], 400);
        }

        // Validate request data
        $validated = $request->validate([
            'title' => 'nullable|string|min:1|max:255',
            'content' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'primary_category' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string',
        ]);

        // Validate primary category is in selected categories
        if ($request->primary_category && !in_array($request->primary_category, $request->categories ?? [])) {
            $validated['primary_category'] = null; // Remove invalid primary category
        }

        try {
            // Update post using service (reuses existing logic)
            $this->postService->updatePost($post, array_merge($validated, [
                'status' => 'draft', // Ensure it stays as draft
                'thumbnail' => $post->thumbnail, // Preserve existing thumbnail
                'seo_title' => $post->seo_title, // Preserve SEO fields
                'seo_description' => $post->seo_description,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Draft saved',
                'timestamp' => now()->format('H:i'),
                'saved_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions on posts.
     */
    public function bulkAction(Request $request)
{
    $request->validate([
        'action'   => 'required|string',
        'post_ids' => 'required|array|min:1',
        'post_ids.*' => 'integer|exists:posts,id',
    ]);

    $action   = $request->action;
    $postIds  = $request->post_ids;

    $successCount = 0;
    $errors = [];

    $posts = Post::whereIn('id', $postIds)->get();

    DB::transaction(function () use ($action, $posts, &$successCount, &$errors) {

        foreach ($posts as $post) {
            try {
                switch ($action) {

                    case 'publish':
                        if ($post->status !== 'published') {
                            $post->update([
                                'status'       => 'published',
                                'published_at' => now(),
                            ]);
                        }
                        $successCount++;
                        break;

                    case 'draft':
                        $post->update([
                            'status'       => 'draft',
                            'published_at' => null,
                        ]);
                        $successCount++;
                        break;

                    case 'delete':
                        $post->delete(); // soft delete
                        $successCount++;
                        break;

                    default:
                        $errors[] = $post->id;
                        break;
                }
            } catch (\Throwable $e) {
                $errors[] = $post->id;
            }
        }
    });

    // Build success message
    $message = $successCount . " post" . ($successCount > 1 ? 's' : '') . " processed successfully.";

    // FIXED SYNTAX (đoạn bạn bị lỗi)
    if (!empty($errors)) {
        $message .= " " . count($errors) . " post" . (count($errors) > 1 ? 's' : '') . " could not be processed.";
    }
    return redirect()->back()->with('success', $message);
    }

    public function suggestTags(Request $request)
    {
        $query = $request->get('q', '');
        $tags = Tag::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);
        return response()->json($tags);
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }
}
