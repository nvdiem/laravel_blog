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

    // Get primary category
    public function primaryCategory()
    {
        return $this->categories()->wherePivot('is_primary', true)->first();
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
}
