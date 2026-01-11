<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'allow_index',
        'created_by',
        'storage_path', // Legacy field - keep for backward compatibility
        'bundle_disk',
        'bundle_path',
        'bundle_version',
    ];

    protected $guarded = [
        'public_token',
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            // Auto-generate public_token if not set
            if (empty($page->public_token)) {
                $page->public_token = Str::random(32);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
