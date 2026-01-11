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
        $maxSize = config('cms.security.upload_limits.max_bundle_size', 50 * 1024 * 1024); // 50MB

        $request->validate([
            'bundle' => 'required|file|mimes:zip|max:' . ($maxSize / 1024), // Laravel expects KB
        ]);

        $file = $request->file('bundle');
        $tempPath = $file->getRealPath();

        try {
            // Create versioned storage path
            $version = date('Y-m-d_H-i-s');
            $bundlePath = config('cms.storage.page_bundles.path') . "/{$page->id}/{$version}";
            $disk = config('cms.storage.page_bundles.disk');

            // First, extract to temp directory for validation
            $tempExtractPath = public_path(config('cms.storage.temp.path') . '/bundle_extract_' . uniqid());
            if (!mkdir($tempExtractPath, 0755, true)) {
                throw new \Exception('Failed to create temporary extraction directory.');
            }

            // Validate ZIP file and contents
            $zip = new ZipArchive();
            if ($zip->open($tempPath) !== true) {
                $this->cleanupTempDir($tempExtractPath);
                return back()->with('error', 'Invalid ZIP file.');
            }

            // Validate ZIP contents
            $validationResult = $this->validateZipContents($zip);
            if (!$validationResult['valid']) {
                $zip->close();
                $this->cleanupTempDir($tempExtractPath);
                return back()->with('error', $validationResult['message']);
            }

            // Extract to temporary directory for final validation
            $zip->extractTo($tempExtractPath);
            $zip->close();

            // Additional validation on extracted files
            $finalValidation = $this->validateExtractedFiles($tempExtractPath);
            if (!$finalValidation['valid']) {
                $this->cleanupTempDir($tempExtractPath);
                return back()->with('error', $finalValidation['message']);
            }

            // Move validated files to final storage
            $this->moveBundleToStorage($tempExtractPath, $bundlePath, $disk);

            // Clean up temp directory
            $this->cleanupTempDir($tempExtractPath);

            // Update page with new bundle info
            $page->update([
                'bundle_disk' => $disk,
                'bundle_path' => $bundlePath,
                'bundle_version' => $version,
            ]);

            return back()->with('success', 'Page bundle uploaded successfully.');

        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempExtractPath) && file_exists($tempExtractPath)) {
                $this->cleanupTempDir($tempExtractPath);
            }

            \Illuminate\Support\Facades\Log::error('Bundle upload failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to process bundle: ' . $e->getMessage());
        }
    }

    /**
     * Validate ZIP file contents before extraction
     */
    private function validateZipContents(ZipArchive $zip): array
    {
        $hasIndexHtml = false;
        $totalSize = 0;
        $maxSize = config('cms.security.upload_limits.max_bundle_size', 50 * 1024 * 1024);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $fileInfo = $zip->statIndex($i);

            // Check for allowed bundle extensions only
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = config('cms.security.allowed_bundle_extensions', []);

            if (!in_array($extension, $allowedExtensions)) {
                return [
                    'valid' => false,
                    'message' => "ZIP contains file with disallowed extension: {$filename} (only web-safe files allowed)"
                ];
            }

            // Additional security check for dangerous files
            if ($this->isDangerousFile($filename)) {
                return [
                    'valid' => false,
                    'message' => "ZIP contains dangerous file: {$filename}"
                ];
            }

            // Check file size
            $totalSize += $fileInfo['size'];
            if ($totalSize > $maxSize) {
                return [
                    'valid' => false,
                    'message' => 'Bundle size exceeds maximum allowed size.'
                ];
            }

            // Check for index.html at root
            if ($filename === 'index.html') {
                $hasIndexHtml = true;
            }

            // Check directory depth (prevent zip bombs)
            $pathParts = explode('/', $filename);
            if (count($pathParts) > 10) { // Max 10 levels deep
                return [
                    'valid' => false,
                    'message' => 'Bundle contains files too deep in directory structure.'
                ];
            }
        }

        if (!$hasIndexHtml) {
            return [
                'valid' => false,
                'message' => 'ZIP must contain index.html at the root level.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate extracted files
     */
    private function validateExtractedFiles(string $extractPath): array
    {
        $indexHtmlPath = $extractPath . '/index.html';
        if (!file_exists($indexHtmlPath)) {
            return [
                'valid' => false,
                'message' => 'index.html not found after extraction.'
            ];
        }

        // Additional security checks can be added here
        // e.g., scan for malicious content, validate HTML structure, etc.

        return ['valid' => true];
    }

    /**
     * Move validated bundle to final storage
     */
    private function moveBundleToStorage(string $sourcePath, string $bundlePath, string $disk): void
    {
        $files = $this->getAllFiles($sourcePath);

        foreach ($files as $file) {
            if (is_file($file)) {
                // Get relative path from source directory
                // Normalize path separators for cross-platform compatibility
                $normalizedSourcePath = rtrim(str_replace('\\', '/', $sourcePath), '/');
                $normalizedFile = str_replace('\\', '/', $file);

                $relativePath = str_replace($normalizedSourcePath . '/', '', $normalizedFile);
                $storagePath = $bundlePath . '/' . $relativePath;

                // Ensure directory exists (Storage::put doesn't create directories automatically)
                $directory = dirname($storagePath);
                if ($directory !== '.' && $directory !== '/') {
                    Storage::disk($disk)->makeDirectory($directory);
                }

                // Store file
                Storage::disk($disk)->put($storagePath, file_get_contents($file));
            }
        }
    }

    /**
     * Recursively get all files in directory
     */
    private function getAllFiles(string $directory): array
    {
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Clean up temporary directory
     */
    private function cleanupTempDir(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    /**
     * Check if file is dangerous (PHP files, symlinks, etc.)
     */
    private function isDangerousFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Block forbidden extensions
        if (in_array($extension, config('cms.security.forbidden_extensions', []))) {
            return true;
        }

        // Block path traversal attempts
        if (strpos($filename, '..') !== false) {
            return true;
        }

        // Block hidden files
        if (strpos(basename($filename), '.') === 0) {
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
