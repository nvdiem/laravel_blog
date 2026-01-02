<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'size',
        'created_by',
    ];

    protected $appends = ['url'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
