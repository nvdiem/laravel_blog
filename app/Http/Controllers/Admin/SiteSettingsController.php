<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function index(): View
    {
        $this->authorize('system.configure');

        $settings = [
            'site_name' => SiteSetting::get('site_name', config('app.name')),
            'site_logo' => SiteSetting::get('site_logo'),
            'primary_color' => SiteSetting::get('primary_color', '#6366f1'),
            'seo_title' => SiteSetting::get('seo_title'),
            'seo_description' => SiteSetting::get('seo_description'),
        ];

        return view('admin.site-settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('system.configure');

        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            // Delete old logo if exists
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store new logo
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $validated['site_logo'] = $logoPath;
        }

        // Update settings
        foreach ($validated as $key => $value) {
            if ($key === 'site_logo') {
                SiteSetting::set($key, $value, 'image');
            } elseif ($key === 'primary_color') {
                SiteSetting::set($key, $value, 'color');
            } elseif (in_array($key, ['seo_title', 'seo_description'])) {
                SiteSetting::set($key, $value, 'text');
            } else {
                SiteSetting::set($key, $value, 'string');
            }
        }

        return redirect()->route('admin.site-settings.index')
            ->with('success', 'Site settings updated successfully.');
    }
}
