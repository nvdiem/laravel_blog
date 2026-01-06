<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable timestamps since our migration doesn't include them

    protected $fillable = [
        'post_id',
        'viewed_at',
        'ip_hash',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Relationship to post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
