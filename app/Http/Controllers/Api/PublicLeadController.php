<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PublicLeadController extends Controller
{
    /**
     * Submit a lead from a public page.
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            // Validate request structure
            $request->validate([
                'page_slug' => 'required|string',
                'form_key' => 'nullable|string',
                'data' => 'required|array',
            ]);

            $pageSlug = $request->input('page_slug');
            $formKey = $request->input('form_key');
            $data = $request->input('data');

            // Find page by slug
            $page = Page::where('slug', $pageSlug)->first();

            // Don't reveal if page exists - always return generic error for security
            if (!$page || $page->status !== 'published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 400);
            }

            // Verify X-PAGE-TOKEN header
            $pageToken = $request->header('X-PAGE-TOKEN');
            if (!$pageToken || $pageToken !== $page->public_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 400);
            }

            // Validate data contains at least email or phone
            if (empty($data['email']) && empty($data['phone'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email or phone is required'
                ], 400);
            }

            // Extract common fields
            $name = $data['name'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['phone'] ?? null;

            // Save lead
            Lead::create([
                'page_id' => $page->id,
                'form_key' => $formKey,
                'payload' => $data,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead submitted successfully'
            ]);

        } catch (\Exception $e) {
            // Log the error but don't expose internal details
            Log::error('Lead submission error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }
}
