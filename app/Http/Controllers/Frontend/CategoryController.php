<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the category archive page.
     */
    public function show($slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        // Get published posts for this category
        $posts = $category->posts()
            ->with('categories')
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        // SEO
        $seoTitle = $category->name;
        $seoDescription = $category->description ?: "Browse all posts in {$category->name} category";

        return view('frontend.categories.show', compact('category', 'posts', 'seoTitle', 'seoDescription'));
    }
}
