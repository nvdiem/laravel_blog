<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class UpdateService
{
    protected $stateFile = 'update_state.json';
    protected $lockFile = 'update.lock';

    /**
     * Check if update is available
     */
    public function checkForUpdates(): array
    {
        $currentVersion = SiteSetting::get('app_version', '1.0.0');
        $latestVersion = $this->getLatestVersion();

        return [
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'update_available' => version_compare($latestVersion, $currentVersion, '>'),
            'changelog' => $this->getChangelog($currentVersion, $latestVersion)
        ];
    }

    /**
     * Get latest version (mock implementation - in real app, check remote API)
     */
    protected function getLatestVersion(): string
    {
        // This would typically check a remote API for latest version
        // For demo purposes, return a version higher than current
        return '1.1.0';
    }

    /**
     * Get changelog between versions
     */
    protected function getChangelog(string $from, string $to): array
    {
        // This would typically fetch changelog from remote API
        // For demo purposes, return mock changelog
        return [
            '1.1.0' => [
                'Security fixes',
                'Performance improvements',
                'New features for content management',
                'Bug fixes'
            ]
        ];
    }

    /**
     * Check if update is locked
     */
    public function isLocked(): bool
    {
        return Storage::disk('local')->exists($this->lockFile);
    }

    /**
     * Lock update process
     */
    public function lock(): void
    {
        Storage::disk('local')->put($this->lockFile, now()->toISOString());
    }

    /**
     * Unlock update process
     */
    public function unlock(): void
    {
        Storage::disk('local')->delete($this->lockFile);
    }

    /**
     * Get current update state
     */
    public function getState(): array
    {
        if (!Storage::disk('local')->exists($this->stateFile)) {
            return [
                'step' => 1,
                'completed_steps' => [],
                'data' => [],
                'version' => null
            ];
        }

        return json_decode(Storage::disk('local')->get($this->stateFile), true);
    }

    /**
     * Save update state
     */
    public function saveState(array $state): void
    {
        Storage::disk('local')->put($this->stateFile, json_encode($state, JSON_PRETTY_PRINT));
    }

    /**
     * Clear update state
     */
    public function clearState(): void
    {
        Storage::disk('local')->delete($this->stateFile);
    }

    /**
     * Step 1: Pre-update checks
     */
    public function preUpdateChecks(): array
    {
        $checks = [
            'backup_exists' => [
                'name' => 'Database Backup',
                'status' => $this->hasRecentBackup(),
                'message' => 'Recent database backup recommended'
            ],
            'storage_writable' => [
                'name' => 'Storage Writable',
                'status' => is_writable(storage_path()),
                'message' => 'Storage directory must be writable'
            ],
            'maintenance_mode' => [
                'name' => 'Maintenance Mode',
                'status' => app()->isDownForMaintenance(),
                'message' => 'Site should be in maintenance mode during update'
            ],
            'disk_space' => [
                'name' => 'Disk Space',
                'status' => $this->checkDiskSpace(),
                'message' => 'At least 100MB free disk space required'
            ]
        ];

        $passed = collect($checks)->every(fn($check) => $check['status']);

        return [
            'passed' => $passed,
            'checks' => $checks
        ];
    }

    /**
     * Check if recent backup exists
     */
    protected function hasRecentBackup(): bool
    {
        // Check for backup files created within last 7 days
        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            return false;
        }

        $files = glob($backupPath . '/*.sql');
        foreach ($files as $file) {
            if (filemtime($file) > strtotime('-7 days')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check available disk space
     */
    protected function checkDiskSpace(): bool
    {
        $freeSpace = disk_free_space(base_path());
        return $freeSpace > 100 * 1024 * 1024; // 100MB
    }

    /**
     * Step 2: Download and extract update files
     */
    public function downloadUpdate(string $version): bool
    {
        try {
            // In a real implementation, this would:
            // 1. Download update package from remote server
            // 2. Verify package integrity
            // 3. Extract files to temporary location
            // 4. Backup current files

            // For demo, we'll simulate this process
            sleep(1); // Simulate download time

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to download update: ' . $e->getMessage());
        }
    }

    /**
     * Step 3: Run database migrations
     */
    public function runMigrations(): bool
    {
        try {
            // Run any pending migrations
            Artisan::call('migrate', ['--force' => true]);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Step 4: Update application files
     */
    public function updateFiles(): bool
    {
        try {
            // In a real implementation, this would:
            // 1. Copy new files to application directory
            // 2. Update configuration files
            // 3. Clear compiled views and caches

            // For demo, simulate file updates
            sleep(1);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('File update failed: ' . $e->getMessage());
        }
    }

    /**
     * Step 5: Update data and settings
     */
    public function updateData(): void
    {
        try {
            // Update version in settings
            SiteSetting::set('app_version', $this->getLatestVersion());
            SiteSetting::set('updated_at', now()->toISOString());

            // Clear various caches
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Run seeders if needed
            // Artisan::call('db:seed', ['--class' => 'UpdateSeeder']);

        } catch (\Exception $e) {
            throw new \Exception('Data update failed: ' . $e->getMessage());
        }
    }

    /**
     * Step 6: Post-update cleanup
     */
    public function postUpdateCleanup(): void
    {
        try {
            // Clear temporary files
            $this->clearTemporaryFiles();

            // Update file permissions if needed
            $this->updatePermissions();

            // Log update completion
            $this->logUpdate();

        } catch (\Exception $e) {
            throw new \Exception('Post-update cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear temporary update files
     */
    protected function clearTemporaryFiles(): void
    {
        // Remove temporary update files
        $tempPath = storage_path('app/temp');
        if (file_exists($tempPath)) {
            $this->deleteDirectory($tempPath);
        }
    }

    /**
     * Update file permissions
     */
    protected function updatePermissions(): void
    {
        // Update permissions for storage and bootstrap cache
        $paths = [
            storage_path(),
            base_path('bootstrap/cache')
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                chmod($path, 0755);
            }
        }
    }

    /**
     * Log update completion
     */
    protected function logUpdate(): void
    {
        $logData = [
            'version' => SiteSetting::get('app_version'),
            'updated_at' => now()->toISOString(),
            'updated_by' => auth()->id() ?? 'system'
        ];

        Storage::disk('local')->put(
            'update_log.json',
            json_encode($logData, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Recursively delete directory
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * Complete update
     */
    public function completeUpdate(): void
    {
        $this->clearState();
        $this->unlock();

        // Take site out of maintenance mode if it was put in
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
        }
    }

    /**
     * Rollback update (if something goes wrong)
     */
    public function rollback(): void
    {
        try {
            // This would restore from backup
            // For demo, just clear state and unlock
            $this->clearState();
            $this->unlock();

            // Restore site from maintenance if needed
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
            }

        } catch (\Exception $e) {
            // Log rollback failure
            \Illuminate\Support\Facades\Log::error('Update rollback failed: ' . $e->getMessage());
        }
    }

    /**
     * Get update steps
     */
    public function getSteps(): array
    {
        return [
            1 => ['name' => 'Pre-Update Checks', 'description' => 'Verifying system requirements and backups'],
            2 => ['name' => 'Download Update', 'description' => 'Downloading and preparing update files'],
            3 => ['name' => 'Database Migration', 'description' => 'Running database migrations'],
            4 => ['name' => 'File Updates', 'description' => 'Updating application files'],
            5 => ['name' => 'Data Updates', 'description' => 'Updating settings and data'],
            6 => ['name' => 'Cleanup', 'description' => 'Final cleanup and optimization'],
        ];
    }
}
