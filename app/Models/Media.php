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
        return $this->getPublicUrl();
    }

    /**
     * Get public URL for the media file
     */
    public function getPublicUrl(): string
    {
        // New content structure (Mode A)
        if ($this->disk === config('cms.storage.media.disk')) {
            return url(config('cms.storage.media.url_base') . '/' . $this->file_path);
        }

        // Backward compatibility for old data
        if (config('cms.backward_compatibility.enabled') &&
            $this->disk === config('cms.backward_compatibility.old_media_disk')) {
            return asset('storage/' . $this->file_path);
        }

        // Fallback to Storage URL (construct manually since some disks may not have url config)
        $diskConfig = config("filesystems.disks.{$this->disk}");
        if (isset($diskConfig['url'])) {
            return rtrim($diskConfig['url'], '/') . '/' . $this->file_path;
        }

        // Last resort - construct basic URL
        return url($this->file_path);
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
