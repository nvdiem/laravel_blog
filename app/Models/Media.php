<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'size',
        'alt_text',
        'width',
        'height',
        'created_by',
    ];

    protected $appends = ['url', 'formatted_size', 'dimensions'];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'mediable')
                    ->withPivot('context')
                    ->withTimestamps();
    }

    /**
     * Accessors
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < 3; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensionsAttribute(): ?string
    {
        if (!$this->width || !$this->height) return null;
        return "{$this->width} × {$this->height}";
    }

    /**
     * Helper methods
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isUsed(): bool
    {
        return $this->posts()->exists();
    }
}
