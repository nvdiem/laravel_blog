<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail_id', // Changed from 'thumbnail' to store Media ID
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

    // Thumbnail relationship (belongsTo Media)
    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    // Get featured/thumbnail image (legacy morph relationship)
    public function featuredImage()
    {
        return $this->morphToMany(Media::class, 'mediable')
                    ->wherePivot('context', 'thumbnail')
                    ->first();
    }

    // Get thumbnail URL accessor
    public function getThumbnailUrlAttribute()
    {
        // First try new thumbnail relationship
        if ($this->thumbnail && is_object($this->thumbnail)) {
            return $this->thumbnail->getPublicUrl();
        }

        // Fallback to legacy featuredImage
        $featuredImage = $this->featuredImage();
        if ($featuredImage) {
            return $featuredImage->getPublicUrl();
        }

        // Fallback to old thumbnail_id field (for backward compatibility)
        if ($this->thumbnail_id && is_numeric($this->thumbnail_id)) {
            $media = Media::find($this->thumbnail_id);
            if ($media) {
                return $media->getPublicUrl();
            }
        }

        // Last fallback - if thumbnail_id contains a URL directly (for migration)
        if ($this->thumbnail_id && filter_var($this->thumbnail_id, FILTER_VALIDATE_URL)) {
            return $this->thumbnail_id;
        }

        // Check old thumbnail field for backward compatibility
        if ($this->getRawOriginal('thumbnail') && filter_var($this->getRawOriginal('thumbnail'), FILTER_VALIDATE_URL)) {
            return $this->getRawOriginal('thumbnail');
        }

        return null;
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
        return $query->select('posts.id', DB::raw('COUNT(post_views.id) as view_count'))
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
