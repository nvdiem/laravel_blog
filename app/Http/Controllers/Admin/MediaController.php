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
     * Display a listing of the resource.
     */
    public function index()
    {
        $media = Media::latest()->paginate(20);
        return response()->json($media);
    }

    /**
     * Handle media upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $extension;

        // Store path: /media/YEAR/MONTH/
        $path = 'media/' . date('Y') . '/' . date('m') . '/' . $filename;

        // Store file
        $storedPath = $file->storeAs('media/' . date('Y') . '/' . date('m'), $filename, 'public');

        // Save to database
        $media = Media::create([
            'file_name' => $originalName,
            'file_path' => $storedPath,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => $media->url,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        //
    }
}
