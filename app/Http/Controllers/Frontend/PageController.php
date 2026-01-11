<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();

        // Page not found
        if (!$page) {
            abort(404);
        }

        // Page not published
        if ($page->status !== 'published') {
            abort(404);
        }

        // Check if page has bundle (new system or legacy)
        $bundleInfo = $this->getBundleInfo($page);

        if (!$bundleInfo) {
            return response()->view('frontend.pages.coming-soon', compact('page'), 200);
        }

        // Load and serve the page HTML
        $htmlPath = $bundleInfo['full_path'];

        if (!file_exists($htmlPath)) {
            return response()->view('frontend.pages.coming-soon', compact('page'), 200);
        }

        $htmlContent = file_get_contents($htmlPath);

        // Inject CMS configuration before </head> or </body>
        $cmsConfig = $this->getCmsConfigScript($page);
        $htmlContent = $this->injectCmsConfig($htmlContent, $cmsConfig);

        // Fix asset paths to be absolute URLs
        $htmlContent = $this->fixAssetPaths($htmlContent, $bundleInfo);

        return response($htmlContent, 200)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Generate CMS configuration script
     */
    private function getCmsConfigScript(Page $page): string
    {
        return <<<HTML
<script>
window.CMS_PAGE = {
  page_id: {$page->id},
  page_slug: "{$page->slug}",
  api: "/api/public/leads/submit",
  token: "{$page->public_token}"
};
</script>
HTML;
    }

    /**
     * Inject CMS config before </head> or </body>
     */
    private function injectCmsConfig(string $html, string $configScript): string
    {
        // Try to inject before </head>
        if (preg_match('/<\/head>/i', $html)) {
            return preg_replace('/<\/head>/i', $configScript . '</head>', $html);
        }

        // Fallback: inject before </body>
        if (preg_match('/<\/body>/i', $html)) {
            return preg_replace('/<\/body>/i', $configScript . '</body>', $html);
        }

        // Last resort: append to end
        return $html . $configScript;
    }

    /**
     * Get bundle information for the page (new or legacy)
     */
    private function getBundleInfo(Page $page): ?array
    {
        // Check for new bundle system first
        if ($page->bundle_disk && $page->bundle_path) {
            $disk = $page->bundle_disk;
            $path = $page->bundle_path;

            // Check if file exists in new system
            if (Storage::disk($disk)->exists($path . '/index.html')) {
                $diskConfig = config("filesystems.disks.{$disk}");

                // For content disks, construct path relative to public/content
                if (str_starts_with($disk, 'content_')) {
                    $fullPath = public_path('content/' . $path . '/index.html');
                    $baseUrl = rtrim($diskConfig['url'] ?? url('/content'), '/') . '/' . $path;
                } else {
                    // Fallback for other disks - construct path manually
                    $rootPath = $diskConfig['root'] ?? '';
                    $fullPath = rtrim($rootPath, '/') . '/' . $path . '/index.html';
                    $baseUrl = rtrim($diskConfig['url'] ?? '', '/') . '/' . $path;
                }

                return [
                    'disk' => $disk,
                    'path' => $path,
                    'full_path' => $fullPath,
                    'base_url' => $baseUrl,
                    'is_legacy' => false
                ];
            }
        }

        // Fallback to legacy system (backward compatibility)
        if ($page->storage_path && config('cms.backward_compatibility.enabled')) {
            $legacyPath = storage_path("app/public/{$page->storage_path}/index.html");
            if (file_exists($legacyPath)) {
                return [
                    'disk' => 'public',
                    'path' => $page->storage_path,
                    'full_path' => $legacyPath,
                    'base_url' => asset("storage/{$page->storage_path}"),
                    'is_legacy' => true
                ];
            }
        }

        return null;
    }

    /**
     * Fix relative asset paths to absolute URLs
     */
    private function fixAssetPaths(string $html, array $bundleInfo): string
    {
        $baseUrl = $bundleInfo['base_url'];

        // Convert relative paths to absolute URLs
        $html = preg_replace_callback('/(src|href)=["\']([^"\']*)["\']/', function($matches) use ($baseUrl) {
            $attr = $matches[1];
            $path = $matches[2];

            // Skip if already absolute URL or external
            if (filter_var($path, FILTER_VALIDATE_URL) || strpos($path, '//') === 0) {
                return $matches[0];
            }

            // Skip if starts with #
            if (strpos($path, '#') === 0) {
                return $matches[0];
            }

            // Convert relative path to absolute
            if (strpos($path, './') === 0) {
                $path = substr($path, 2);
            }

            return "{$attr}=\"{$baseUrl}/{$path}\"";
        }, $html);

        return $html;
    }
}
