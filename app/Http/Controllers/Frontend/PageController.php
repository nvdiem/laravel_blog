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

        // Page content not ready yet (storage_path is null)
        if (is_null($page->storage_path)) {
            return response()->view('frontend.pages.coming-soon', compact('page'), 200);
        }

        // Load and serve the page HTML
        $htmlPath = storage_path("app/public/{$page->storage_path}/index.html");

        if (!file_exists($htmlPath)) {
            return response()->view('frontend.pages.coming-soon', compact('page'), 200);
        }

        $htmlContent = file_get_contents($htmlPath);

        // Inject CMS configuration before </head> or </body>
        $cmsConfig = $this->getCmsConfigScript($page);
        $htmlContent = $this->injectCmsConfig($htmlContent, $cmsConfig);

        // Fix asset paths to be absolute URLs
        $htmlContent = $this->fixAssetPaths($htmlContent, $page->storage_path);

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
     * Fix relative asset paths to absolute URLs
     */
    private function fixAssetPaths(string $html, string $storagePath): string
    {
        $baseUrl = asset("storage/{$storagePath}");

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
