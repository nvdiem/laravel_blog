<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Create a new post.
     */
    public function createPost(array $data): Post
    {
        $slug = $this->generateSlug($data['title']);
        $thumbnailPath = $this->handleFileUpload($data['thumbnail'] ?? null);

        $post = Post::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'thumbnail' => $thumbnailPath,
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
        ]);

        if (isset($data['tags'])) {
            $this->tagService->syncTags($post, $data['tags']);
        }

        return $post;
    }

    /**
     * Update an existing post.
     */
    public function updatePost(Post $post, array $data): Post
    {
        $slug = $this->generateSlug($data['title'], $post->id);
        $thumbnailPath = $this->handleFileUpdate($post, $data['thumbnail'] ?? null);

        $post->update([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'thumbnail' => $thumbnailPath,
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
        ]);

        if (isset($data['tags'])) {
            $this->tagService->syncTags($post, $data['tags']);
        }

        return $post;
    }

    /**
     * Generate unique slug for post.
     */
    protected function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = Post::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = Post::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Handle file upload for new post.
     */
    protected function handleFileUpload(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store('posts', 'public');
    }

    /**
     * Handle file update for existing post.
     */
    protected function handleFileUpdate(Post $post, ?UploadedFile $file): ?string
    {
        if (!$file) {
            return $post->thumbnail;
        }

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        return $file->store('posts', 'public');
    }
}
