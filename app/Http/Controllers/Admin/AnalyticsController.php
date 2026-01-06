<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display admin analytics dashboard.
     */
    public function index()
    {
        // Total views - all time
        $totalViews = PostView::count();

        // Views in last 7 days
        $viewsLast7Days = PostView::where('viewed_at', '>=', now()->subDays(7))->count();

        // Views in last 30 days
        $viewsLast30Days = PostView::where('viewed_at', '>=', now()->subDays(30))->count();

        // Trend calculations
        $viewsPrevious7Days = PostView::whereBetween('viewed_at', [
            now()->subDays(14),
            now()->subDays(7)
        ])->count();

        $viewsPrevious30Days = PostView::whereBetween('viewed_at', [
            now()->subDays(60),
            now()->subDays(30)
        ])->count();

        $trend7Days = $this->calculateTrend($viewsLast7Days, $viewsPrevious7Days);
        $trend30Days = $this->calculateTrend($viewsLast30Days, $viewsPrevious30Days);

        // Top posts by views (last 7 days)
        $topPosts7Days = Post::select('posts.*', DB::raw('COUNT(post_views.id) as views_count'))
            ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
            ->where('post_views.viewed_at', '>=', now()->subDays(7))
            ->groupBy('posts.id')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        // Top posts by views (last 30 days)
        $topPosts30Days = Post::select('posts.*', DB::raw('COUNT(post_views.id) as views_count'))
            ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
            ->where('post_views.viewed_at', '>=', now()->subDays(30))
            ->groupBy('posts.id')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.index', compact(
            'totalViews',
            'viewsLast7Days',
            'viewsLast30Days',
            'trend7Days',
            'trend30Days',
            'topPosts7Days',
            'topPosts30Days'
        ));
    }

    /**
     * Calculate trend direction based on current vs previous period.
     */
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 'up' : 'stable';
        }

        $change = (($current - $previous) / $previous) * 100;

        if ($change > 5) return 'up';
        if ($change < -5) return 'down';
        return 'stable';
    }
}
