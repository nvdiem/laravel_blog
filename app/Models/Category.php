<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Parent category (self-referencing)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Posts relationship (many-to-many)
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withPivot('is_primary');
    }

    // Scope for active categories
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Get full hierarchy path
    public function getPathAttribute(): string
    {
        $path = $this->name;
        $parent = $this->parent;

        while ($parent) {
            $path = $parent->name . ' → ' . $path;
            $parent = $parent->parent;
        }

        return $path;
    }
}
