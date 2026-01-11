<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of media files.
     */
    public function index(Request $request)
    {
        $query = Media::with('creator')->latest();

        // Search by filename
        if ($search = $request->search) {
            $query->where('file_name', 'like', "%{$search}%");
        }

        // Filter by file type
        if ($type = $request->type) {
            if ($type === 'images') {
                $query->where('mime_type', 'like', 'image/%');
            }
        }

        // Filter by usage status (using relationship)
        if ($usage = $request->usage) {
            if ($usage === 'used') {
                $query->has('posts');
            } elseif ($usage === 'unused') {
                $query->doesntHave('posts');
            }
        }

        $media = $query->paginate(config('media.pagination.per_page', 24));
        $totalSize = Media::sum('size');
        $totalCount = Media::count();

        // If JSON request (for modal), return JSON
        if ($request->expectsJson()) {
            return response()->json($media);
        }

        return view('admin.media.index', compact('media', 'totalSize', 'totalCount'));
    }

    /**
     * Handle media file upload.
     */
    public function upload(Request $request)
    {
        try {
            $maxSize = config('cms.security.upload_limits.max_file_size', 10 * 1024 * 1024); // 10MB
            $maxFiles = config('cms.security.upload_limits.max_files_per_upload', 10);

            $request->validate([
                'files' => 'required|array|max:' . $maxFiles,
                'files.*' => "required|file|max:{$maxSize}|image|mimes:jpg,jpeg,png,webp,gif",
            ]);

            $files = $request->file('files');

            if (!$files || count($files) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files were uploaded.',
                ], 400);
            }

            $uploaded = [];

            foreach ($files as $file) {
                // Security validation
                if (!$this->isFileAllowed($file)) {
                    continue; // Skip dangerous files
                }

                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = time() . '_' . uniqid() . '.' . $extension;

                // Store in organized structure: uploads/YYYY/MM/filename.ext
                $relativePath = config('cms.storage.media.path') . '/' . date('Y') . '/' . date('m') . '/' . $filename;

                $disk = config('cms.storage.media.disk');
                Storage::disk($disk)->put($relativePath, file_get_contents($file->getRealPath()));

                // Extract image dimensions
                $dimensions = @getimagesize($file->getRealPath());

                $media = Media::create([
                    'file_name' => $originalName,
                    'file_path' => $relativePath,
                    'disk' => $disk,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null,
                    'created_by' => Auth::id(),
                ]);

                $uploaded[] = $media;
            }

            if (empty($uploaded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid files were uploaded.',
                ], 400);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'media' => $uploaded,
                    'message' => count($uploaded) . ' file(s) uploaded successfully.',
                ]);
            }

            return back()->with('success', count($uploaded) . ' file(s) uploaded successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Media upload error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['upload' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if file is allowed (security validation)
     */
    private function isFileAllowed($file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // Check forbidden extensions
        if (in_array($extension, config('cms.security.forbidden_extensions', []))) {
            \Illuminate\Support\Facades\Log::warning("Blocked upload of forbidden file extension: {$extension}");
            return false;
        }

        // Check forbidden MIME types
        if (in_array($mimeType, config('cms.security.forbidden_mime_types', []))) {
            \Illuminate\Support\Facades\Log::warning("Blocked upload of forbidden MIME type: {$mimeType}");
            return false;
        }

        // Additional security checks can be added here
        // e.g., file content scanning, virus scanning, etc.

        return true;
    }

    /**
     * Update media metadata (alt text).
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
        ]);

        $media->update([
            'alt_text' => $request->alt_text,
        ]);

        return back()->with('success', 'Media updated successfully.');
    }

    /**
     * Delete media with safety check.
     */
    public function destroy(Media $media)
    {
        // Check if media is in use (via pivot table)
        if ($media->posts()->exists()) {
            $usageCount = $media->posts()->count();
            return back()->withErrors([
                'media' => "Cannot delete. This file is used in {$usageCount} post(s)."
            ]);
        }

        // Delete physical file from storage
        Storage::disk($media->disk)->delete($media->file_path);

        // Delete database record (cascade will clean pivot table)
        $media->delete();

        return back()->with('success', 'Media deleted successfully.');
    }
}
