<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Parse tag string and sync tags with post.
     */
    public function syncTags($post, string $tagsString)
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
