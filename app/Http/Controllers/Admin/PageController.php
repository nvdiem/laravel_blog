<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $query = Page::with('creator');

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Search by title or slug
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get status counts for filter links
        $allCount = Page::count();
        $publishedCount = Page::where('status', 'published')->count();
        $draftCount = Page::where('status', 'draft')->count();

        return view('admin.pages.index', compact('pages', 'allCount', 'publishedCount', 'draftCount'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'status' => 'required|in:draft,published,disabled',
            'allow_index' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page = new Page();
        $page->title = $validated['title'];
        $page->slug = $validated['slug'];
        $page->status = $validated['status'];
        $page->allow_index = $validated['allow_index'] ?? false;
        $page->created_by = auth()->id();
        $page->save();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page.
     */
    public function show(string $id)
    {
        // Not needed for basic CRUD
        abort(404);
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'status' => 'required|in:draft,published,disabled',
        ]);
        Log::info('BEFORE UPDATE', $page->getAttributes());


        // Explicitly update only the editable fields to prevent data loss
        $page->title = $validated['title'];
        $page->slug = $validated['slug'];
        $page->status = $validated['status'];
        $page->allow_index = $request->has('allow_index');
        $page->created_by = auth()->id();
        $page->save();
        Log::info('AFTER SAVE', $page->fresh()->getAttributes());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Upload bundle for the specified page.
     */
    public function uploadBundle(Request $request, Page $page)
    {
        $request->validate([
            'bundle' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $file = $request->file('bundle');
        $tempPath = $file->getRealPath();

        // Create storage directory path
        $storagePath = "page-builder/pages/{$page->id}";

        try {
            // Validate ZIP file and contents
            $zip = new ZipArchive();
            if ($zip->open($tempPath) !== true) {
                return back()->with('error', 'Invalid ZIP file.');
            }

            // Check for index.html at root level
            $hasIndexHtml = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Security checks
                if ($this->isDangerousFile($filename)) {
                    $zip->close();
                    return back()->with('error', 'ZIP contains forbidden file types.');
                }

                // Check for index.html at root
                if ($filename === 'index.html') {
                    $hasIndexHtml = true;
                }
            }

            if (!$hasIndexHtml) {
                $zip->close();
                return back()->with('error', 'ZIP must contain index.html at the root level.');
            }

            // Clean existing directory
            Storage::disk('public')->deleteDirectory($storagePath);

            // Extract ZIP to storage directory
            $zip->extractTo(storage_path("app/public/{$storagePath}"));
            $zip->close();

            // Update page with storage path
            $page->update([
                'storage_path' => $storagePath,
            ]);

            return back()->with('success', 'Page bundle uploaded successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process bundle: ' . $e->getMessage());
        }
    }

    /**
     * Check if file is dangerous (PHP files, symlinks, etc.)
     */
    private function isDangerousFile(string $filename): bool
    {
        // Block PHP files
        if (preg_match('/\.php$/i', $filename)) {
            return true;
        }

        // Block symlinks and other special files
        // This is a basic check - in production you'd want more comprehensive validation
        if (strpos($filename, '..') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        // Prevent deleting published pages without confirmation
        if ($page->status === 'published') {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Cannot delete published pages. Please disable the page first.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
