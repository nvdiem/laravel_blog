<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'status',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    // Categories relationship (many-to-many)
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('is_primary');
    }

    // Primary category relationship (for eager loading)
    public function primaryCategory()
    {
        return $this->belongsToMany(Category::class)
            ->wherePivot('is_primary', true)
            ->limit(1);
    }

    // Get all categories except primary
    public function secondaryCategories()
    {
        return $this->categories()->wherePivot('is_primary', false);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Media relationship (morphToMany)
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
                    ->withPivot('context')
                    ->withTimestamps();
    }

    // Get featured/thumbnail image
    public function featuredImage()
    {
        return $this->morphToMany(Media::class, 'mediable')
                    ->wherePivot('context', 'thumbnail')
                    ->first();
    }

    // Post views relationship
    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    // Get total view count
    public function getViewCountAttribute()
    {
        return $this->views()->count();
    }

    // Get views in last N days
    public function getViewsInLastDays($days)
    {
        return $this->views()
            ->where('viewed_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Scope a query to only include popular posts based on recent views.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Look back period in days
     * @param int $minViews Minimum views to be considered popular
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query, $days = 7, $minViews = 5)
    {
        return $query->select('posts.*')
            ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
            ->where('post_views.viewed_at', '>=', now()->subDays($days))
            ->groupBy('posts.id')
            ->havingRaw('COUNT(post_views.id) >= ?', [$minViews])
            ->orderByRaw('COUNT(post_views.id) DESC');
    }

    // Increment view count
    public function incrementView($ipAddress = null)
    {
        $ipHash = $ipAddress ? hash('sha256', $ipAddress) : null;

        // Create view record (simple approach - increments on each page load)
        $this->views()->create([
            'viewed_at' => now(),
            'ip_hash' => $ipHash,
        ]);

        return $this;
    }
}
