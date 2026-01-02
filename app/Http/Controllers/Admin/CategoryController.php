<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with(['children', 'posts', 'parent']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Parent filter
        if ($request->filled('parent_id')) {
            if ($request->parent_id === '0') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');

        if (in_array($sortBy, ['name', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('name', 'asc');
        }

        $categories = $query->paginate(20);

        // Calculate post counts for each category
        $categories->getCollection()->transform(function ($category) {
            $category->post_count = $category->posts()->count();
            return $category;
        });

        // For the view, we need all categories to display hierarchy
        // Get all categories without pagination for proper hierarchy display
        $allCategoriesQuery = Category::with(['children', 'posts', 'parent']);

        // Apply same filters
        if ($request->filled('search')) {
            $search = $request->search;
            $allCategoriesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $allCategoriesQuery->where('status', $request->status);
        }

        if ($request->filled('parent_id')) {
            if ($request->parent_id === '0') {
                $allCategoriesQuery->whereNull('parent_id');
            } else {
                $allCategoriesQuery->where('parent_id', $request->parent_id);
            }
        }

        $allCategories = $allCategoriesQuery->orderBy('name', 'asc')->get();

        // Calculate post counts
        $allCategories->transform(function ($category) {
            $category->post_count = $category->posts()->count();
            $category->children->each(function ($child) {
                $child->post_count = $child->posts()->count();
            });
            return $category;
        });

        // Separate parents and children for display
        $parentCategories = $allCategories->where('parent_id', null);
        $childCategories = $allCategories->where('parent_id', '!=', null);

        // Get all parent categories for filter dropdown
        $allParentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories', 'childCategories', 'allParentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get only parent categories (no parent_id) for parent selection
        $parentCategories = Category::whereNull('parent_id')->active()->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Not needed for this implementation
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Get only parent categories (no parent_id) for parent selection
        // Exclude current category and its children to prevent circular references
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->active()
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has posts. Set to inactive instead.');
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has child categories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
