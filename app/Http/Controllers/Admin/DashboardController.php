<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Quick stats
        $stats = [
            'total_posts' => Post::count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];

        // Recent activity - latest 10 updated posts
        $recentPosts = Post::with('categories')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Notices
        $notices = [];
        if ($stats['draft_posts'] > 0) {
            $notices[] = "You have {$stats['draft_posts']} draft post" . ($stats['draft_posts'] > 1 ? 's' : '') . " waiting to be published.";
        }

        return view('admin.dashboard', compact('stats', 'recentPosts', 'notices'));
    }
}
