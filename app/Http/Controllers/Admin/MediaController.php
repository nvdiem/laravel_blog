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
            $maxSize = config('media.upload.max_size', 5120);
            
            $request->validate([
                'files' => 'required|array',
                'files.*' => "image|mimes:jpg,jpeg,png,webp,gif|max:{$maxSize}",
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
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // Store in year/month structure
                $storedPath = $file->storeAs(
                    'media/' . date('Y') . '/' . date('m'),
                    $filename,
                    config('media.storage.disk', 'public')
                );

                // Extract image dimensions
                $dimensions = @getimagesize($file->getRealPath());

                $media = Media::create([
                    'file_name' => $originalName,
                    'file_path' => $storedPath,
                    'disk' => config('media.storage.disk', 'public'),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null,
                    'created_by' => Auth::id(),
                ]);

                $uploaded[] = $media;
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
            \Log::error('Media upload error: ' . $e->getMessage());
            
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
