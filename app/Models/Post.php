<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'status',
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
