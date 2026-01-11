<?php

namespace App\Http\Controllers;

use App\Services\InstallService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InstallController extends Controller
{
    protected InstallService $installService;

    public function __construct(InstallService $installService)
    {
        $this->installService = $installService;
    }

    /**
     * Show installation start page
     */
    public function index(): View|RedirectResponse
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        if ($this->installService->isLocked()) {
            return view('install.locked');
        }

        return view('install.welcome', [
            'steps' => $this->installService->getSteps(),
            'currentStep' => 1
        ]);
    }

    /**
     * Start installation process
     */
    public function start(Request $request): RedirectResponse
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        // Lock the installation process
        $this->installService->lock();

        // Initialize state
        $state = [
            'step' => 1,
            'completed_steps' => [],
            'data' => []
        ];
        $this->installService->saveState($state);

        return redirect()->route('install.step', 1);
    }

    /**
     * Show specific installation step
     */
    public function showStep(int $step): View|RedirectResponse
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        $state = $this->installService->getState();
        $steps = $this->installService->getSteps();

        // Redirect to correct step if needed
        if ($step < $state['step']) {
            return redirect()->route('install.step', $state['step']);
        }

        if ($step > count($steps)) {
            return redirect()->route('install.complete');
        }

        $viewData = [
            'step' => $step,
            'steps' => $steps,
            'currentStep' => $step,
            'state' => $state,
            'nextStep' => $step + 1,
            'prevStep' => $step - 1,
        ];

        switch ($step) {
            case 1:
                return $this->showEnvironmentCheck($viewData);
            case 2:
                return $this->showDatabaseSetup($viewData);
            case 3:
                return $this->showAdminUserForm($viewData);
            case 4:
                return $this->showRolesSetup($viewData);
            case 5:
                return $this->showSiteConfig($viewData);
            case 6:
                return $this->showContentSetup($viewData);
            default:
                return redirect()->route('install.step', 1);
        }
    }

    /**
     * Process installation step
     */
    public function processStep(Request $request, int $step): JsonResponse|RedirectResponse
    {
        if ($this->installService->isInstalled()) {
            return response()->json(['error' => 'Already installed'], 400);
        }

        $state = $this->installService->getState();

        // Validate step sequence
        if ($step !== $state['step']) {
            return response()->json(['error' => 'Invalid step'], 400);
        }

        try {
            switch ($step) {
                case 1:
                    return $this->processEnvironmentCheck($request);
                case 2:
                    return $this->processDatabaseSetup($request);
                case 3:
                    return $this->processAdminUserForm($request);
                case 4:
                    return $this->processRolesSetup($request);
                case 5:
                    return $this->processSiteConfig($request);
                case 6:
                    return $this->processContentSetup($request);
                default:
                    return response()->json(['error' => 'Invalid step'], 400);
            }
        } catch (\Exception $e) {
            // Log error and return to current step with error
            \Illuminate\Support\Facades\Log::error('Installation step ' . $step . ' failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Step failed: ' . $e->getMessage(),
                'redirect' => route('install.step', $step)
            ], 500);
        }
    }

    /**
     * Show environment check step
     */
    protected function showEnvironmentCheck(array $viewData): View
    {
        $viewData['checks'] = $this->installService->checkEnvironment();
        return view('install.environment', $viewData);
    }

    /**
     * Process environment check
     */
    protected function processEnvironmentCheck(Request $request): JsonResponse
    {
        $checks = $this->installService->checkEnvironment();

        if (!$checks['passed']) {
            return response()->json([
                'error' => 'Environment check failed. Please fix the issues and try again.',
                'checks' => $checks['checks']
            ], 400);
        }

        // Mark step as completed and move to next
        $state = $this->installService->getState();
        $state['completed_steps'][] = 1;
        $state['step'] = 2;
        $this->installService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('install.step', 2)
        ]);
    }

    /**
     * Show database setup step
     */
    protected function showDatabaseSetup(array $viewData): View
    {
        return view('install.database', $viewData);
    }

    /**
     * Process database setup
     */
    protected function processDatabaseSetup(Request $request): JsonResponse
    {
        $this->installService->configureDatabase([]);

        // Mark step as completed and move to next
        $state = $this->installService->getState();
        $state['completed_steps'][] = 2;
        $state['step'] = 3;
        $this->installService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('install.step', 3)
        ]);
    }

    /**
     * Show admin user form
     */
    protected function showAdminUserForm(array $viewData): View
    {
        return view('install.admin-user', $viewData);
    }

    /**
     * Process admin user form
     */
    protected function processAdminUserForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $this->installService->createAdminUser($validated);

        // Store data for next steps
        $state = $this->installService->getState();
        $state['data'] = array_merge($state['data'], $validated);
        $state['completed_steps'][] = 3;
        $state['step'] = 4;
        $this->installService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('install.step', 4)
        ]);
    }

    /**
     * Show roles setup step
     */
    protected function showRolesSetup(array $viewData): View
    {
        return view('install.roles', $viewData);
    }

    /**
     * Process roles setup
     */
    protected function processRolesSetup(Request $request): JsonResponse
    {
        $this->installService->setupRolesAndPermissions();

        // Mark step as completed and move to next
        $state = $this->installService->getState();
        $state['completed_steps'][] = 4;
        $state['step'] = 5;
        $this->installService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('install.step', 5)
        ]);
    }

    /**
     * Show site configuration step
     */
    protected function showSiteConfig(array $viewData): View
    {
        return view('install.site-config', $viewData);
    }

    /**
     * Process site configuration
     */
    protected function processSiteConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
        ]);

        $state = $this->installService->getState();
        $data = array_merge($state['data'], $validated);

        $this->installService->setupSiteSettings($data);

        // Mark step as completed and move to next
        $state['data'] = $data;
        $state['completed_steps'][] = 5;
        $state['step'] = 6;
        $this->installService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('install.step', 6)
        ]);
    }

    /**
     * Show content setup step
     */
    protected function showContentSetup(array $viewData): View
    {
        return view('install.content-setup', $viewData);
    }

    /**
     * Process content setup
     */
    protected function processContentSetup(Request $request): JsonResponse
    {
        $this->installService->createContentDirectories();
        $this->installService->completeInstallation();

        return response()->json([
            'success' => true,
            'redirect' => route('install.complete')
        ]);
    }

    /**
     * Show installation complete page
     */
    public function complete(): View|RedirectResponse
    {
        if (!$this->installService->isInstalled()) {
            return redirect()->route('install.index');
        }

        return view('install.complete');
    }

    /**
     * Get installation progress (AJAX)
     */
    public function progress(): JsonResponse
    {
        $state = $this->installService->getState();
        $steps = $this->installService->getSteps();

        return response()->json([
            'current_step' => $state['step'],
            'completed_steps' => $state['completed_steps'],
            'total_steps' => count($steps),
            'progress_percentage' => round((count($state['completed_steps']) / count($steps)) * 100)
        ]);
    }
}
