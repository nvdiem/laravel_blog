<?php

namespace App\Http\Controllers;

use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    protected UpdateService $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
        $this->middleware('auth');
        $this->middleware('can:system.configure');
    }

    /**
     * Show update index page
     */
    public function index(): View
    {
        $updateInfo = $this->updateService->checkForUpdates();

        return view('update.index', [
            'update_available' => $updateInfo['update_available'],
            'current_version' => $updateInfo['current_version'],
            'latest_version' => $updateInfo['latest_version'],
            'changelog' => $updateInfo['changelog'],
            'is_locked' => $this->updateService->isLocked()
        ]);
    }

    /**
     * Start update process
     */
    public function start(Request $request): RedirectResponse
    {
        $updateInfo = $this->updateService->checkForUpdates();

        if (!$updateInfo['update_available']) {
            return redirect()->route('update.index')->with('error', 'No updates available.');
        }

        if ($this->updateService->isLocked()) {
            return redirect()->route('update.index')->with('error', 'Update process is already running.');
        }

        // Lock the update process
        $this->updateService->lock();

        // Initialize state
        $state = [
            'step' => 1,
            'completed_steps' => [],
            'data' => [],
            'version' => $updateInfo['latest_version']
        ];
        $this->updateService->saveState($state);

        return redirect()->route('update.step', 1);
    }

    /**
     * Show specific update step
     */
    public function showStep(int $step): View|RedirectResponse
    {
        if ($this->updateService->isLocked()) {
            return view('update.locked');
        }

        $state = $this->updateService->getState();
        $steps = $this->updateService->getSteps();

        // Redirect to correct step if needed
        if ($step < $state['step']) {
            return redirect()->route('update.step', $state['step']);
        }

        if ($step > count($steps)) {
            return redirect()->route('update.complete');
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
                return $this->showPreUpdateChecks($viewData);
            case 2:
                return $this->showDownloadUpdate($viewData);
            case 3:
                return $this->showDatabaseMigration($viewData);
            case 4:
                return $this->showFileUpdates($viewData);
            case 5:
                return $this->showDataUpdates($viewData);
            case 6:
                return $this->showCleanup($viewData);
            default:
                return redirect()->route('update.step', 1);
        }
    }

    /**
     * Process update step
     */
    public function processStep(Request $request, int $step): JsonResponse|RedirectResponse
    {
        $state = $this->updateService->getState();

        // Validate step sequence
        if ($step !== $state['step']) {
            return response()->json(['error' => 'Invalid step'], 400);
        }

        try {
            switch ($step) {
                case 1:
                    return $this->processPreUpdateChecks($request);
                case 2:
                    return $this->processDownloadUpdate($request);
                case 3:
                    return $this->processDatabaseMigration($request);
                case 4:
                    return $this->processFileUpdates($request);
                case 5:
                    return $this->processDataUpdates($request);
                case 6:
                    return $this->processCleanup($request);
                default:
                    return response()->json(['error' => 'Invalid step'], 400);
            }
        } catch (\Exception $e) {
            // Log error and rollback
            \Illuminate\Support\Facades\Log::error('Update step ' . $step . ' failed: ' . $e->getMessage());

            $this->updateService->rollback();

            return response()->json([
                'error' => 'Step failed: ' . $e->getMessage(),
                'rollback' => true,
                'redirect' => route('update.index')
            ], 500);
        }
    }

    /**
     * Show pre-update checks step
     */
    protected function showPreUpdateChecks(array $viewData): View
    {
        $viewData['checks'] = $this->updateService->preUpdateChecks();
        return view('update.pre-checks', $viewData);
    }

    /**
     * Process pre-update checks
     */
    protected function processPreUpdateChecks(Request $request): JsonResponse
    {
        $checks = $this->updateService->preUpdateChecks();

        if (!$checks['passed']) {
            return response()->json([
                'error' => 'Pre-update checks failed. Please fix the issues and try again.',
                'checks' => $checks['checks']
            ], 400);
        }

        // Mark step as completed and move to next
        $state = $this->updateService->getState();
        $state['completed_steps'][] = 1;
        $state['step'] = 2;
        $this->updateService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('update.step', 2)
        ]);
    }

    /**
     * Show download update step
     */
    protected function showDownloadUpdate(array $viewData): View
    {
        return view('update.download', $viewData);
    }

    /**
     * Process download update
     */
    protected function processDownloadUpdate(Request $request): JsonResponse
    {
        $state = $this->updateService->getState();
        $version = $state['version'] ?? 'latest';

        $this->updateService->downloadUpdate($version);

        // Mark step as completed and move to next
        $state['completed_steps'][] = 2;
        $state['step'] = 3;
        $this->updateService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('update.step', 3)
        ]);
    }

    /**
     * Show database migration step
     */
    protected function showDatabaseMigration(array $viewData): View
    {
        return view('update.migration', $viewData);
    }

    /**
     * Process database migration
     */
    protected function processDatabaseMigration(Request $request): JsonResponse
    {
        $this->updateService->runMigrations();

        // Mark step as completed and move to next
        $state = $this->updateService->getState();
        $state['completed_steps'][] = 3;
        $state['step'] = 4;
        $this->updateService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('update.step', 4)
        ]);
    }

    /**
     * Show file updates step
     */
    protected function showFileUpdates(array $viewData): View
    {
        return view('update.files', $viewData);
    }

    /**
     * Process file updates
     */
    protected function processFileUpdates(Request $request): JsonResponse
    {
        $this->updateService->updateFiles();

        // Mark step as completed and move to next
        $state = $this->updateService->getState();
        $state['completed_steps'][] = 4;
        $state['step'] = 5;
        $this->updateService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('update.step', 5)
        ]);
    }

    /**
     * Show data updates step
     */
    protected function showDataUpdates(array $viewData): View
    {
        return view('update.data', $viewData);
    }

    /**
     * Process data updates
     */
    protected function processDataUpdates(Request $request): JsonResponse
    {
        $this->updateService->updateData();

        // Mark step as completed and move to next
        $state = $this->updateService->getState();
        $state['completed_steps'][] = 5;
        $state['step'] = 6;
        $this->updateService->saveState($state);

        return response()->json([
            'success' => true,
            'redirect' => route('update.step', 6)
        ]);
    }

    /**
     * Show cleanup step
     */
    protected function showCleanup(array $viewData): View
    {
        return view('update.cleanup', $viewData);
    }

    /**
     * Process cleanup
     */
    protected function processCleanup(Request $request): JsonResponse
    {
        $this->updateService->postUpdateCleanup();
        $this->updateService->completeUpdate();

        return response()->json([
            'success' => true,
            'redirect' => route('update.complete')
        ]);
    }

    /**
     * Show update complete page
     */
    public function complete(): View
    {
        $updateInfo = $this->updateService->checkForUpdates();

        return view('update.complete', [
            'current_version' => $updateInfo['current_version'],
            'updated_version' => $updateInfo['current_version']
        ]);
    }

    /**
     * Get update progress (AJAX)
     */
    public function progress(): JsonResponse
    {
        $state = $this->updateService->getState();
        $steps = $this->updateService->getSteps();

        return response()->json([
            'current_step' => $state['step'],
            'completed_steps' => $state['completed_steps'],
            'total_steps' => count($steps),
            'progress_percentage' => round((count($state['completed_steps']) / count($steps)) * 100)
        ]);
    }

    /**
     * Cancel update process
     */
    public function cancel(Request $request): RedirectResponse
    {
        $this->updateService->rollback();

        return redirect()->route('update.index')->with('info', 'Update process has been cancelled.');
    }
}
