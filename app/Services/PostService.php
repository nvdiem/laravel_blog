<?php

namespace App\Services;

use App\Models\Media;
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
        $thumbnailId = $this->handleThumbnail($data['thumbnail'] ?? null);

        $post = Post::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'thumbnail_id' => $thumbnailId, // Changed to thumbnail_id
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        // Sync categories with primary category
        if (isset($data['categories'])) {
            $this->syncCategories($post, $data['categories'], $data['primary_category'] ?? null);
        }

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
        $thumbnailId = $this->handleThumbnail($data['thumbnail'] ?? null);

        $post->update([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'thumbnail_id' => $thumbnailId, // Changed to thumbnail_id
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        // Sync categories with primary category
        if (isset($data['categories'])) {
            $this->syncCategories($post, $data['categories'], $data['primary_category'] ?? null);
        }

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
     * Handle thumbnail for new post (returns Media ID).
     */
    protected function handleThumbnail(mixed $thumbnail): ?int
    {
        if (!$thumbnail) {
            return null;
        }

        // If it's already a Media ID (from media picker)
        if (is_numeric($thumbnail)) {
            return (int) $thumbnail;
        }

        // If it's a URL string, try to find the corresponding Media record
        if (is_string($thumbnail) && filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            // Try to find Media by URL (this is a fallback for migration)
            $media = Media::where('file_path', 'like', '%' . basename(parse_url($thumbnail, PHP_URL_PATH)) . '%')->first();
            if ($media) {
                return $media->id;
            }
        }

        // If it's an uploaded file, we should upload it to media library first
        // For now, return null - thumbnails should be selected from media library
        return null;
    }

    /**
     * Handle file update for existing post.
     */
    protected function handleFileUpdate(Post $post, mixed $file): ?string
    {
        if (!$file) {
            return $post->thumbnail;
        }

        // If it's a string path (from media picker)
        if (is_string($file) && !str_starts_with($file, 'http')) {
            // Delete old thumbnail if different from new one
            if ($post->thumbnail && $post->thumbnail !== $file) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            return $file;
        }

        // If it's an uploaded file
        if ($file instanceof UploadedFile) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            return $file->store('posts', 'public');
        }

        return $post->thumbnail;
    }

    /**
     * Sync categories for a post with primary category.
     */
    protected function syncCategories(Post $post, array $categoryIds, ?int $primaryCategoryId = null): void
    {
        // Prepare pivot data
        $pivotData = [];
        foreach ($categoryIds as $categoryId) {
            $pivotData[$categoryId] = [
                'is_primary' => $categoryId == $primaryCategoryId,
            ];
        }

        // Sync categories with pivot data
        $post->categories()->sync($pivotData);
    }
}
