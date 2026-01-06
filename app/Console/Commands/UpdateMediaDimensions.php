<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateMediaDimensions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:update-dimensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update dimensions for existing media files that don\'t have width/height';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating media dimensions...');

        $media = Media::whereNull('width')
            ->orWhereNull('height')
            ->get();

        if ($media->isEmpty()) {
            $this->info('No media files need updating.');
            return 0;
        }

        $this->info("Found {$media->count()} media files to update.");

        $updated = 0;
        $failed = 0;

        foreach ($media as $item) {
            if (!$item->isImage()) {
                continue;
            }

            try {
                $path = Storage::disk($item->disk)->path($item->file_path);
                
                if (!file_exists($path)) {
                    $this->warn("File not found: {$item->file_path}");
                    $failed++;
                    continue;
                }

                $dimensions = @getimagesize($path);

                if ($dimensions) {
                    $item->update([
                        'width' => $dimensions[0],
                        'height' => $dimensions[1],
                    ]);

                    $this->line("✓ Updated: {$item->file_name} ({$dimensions[0]}×{$dimensions[1]})");
                    $updated++;
                } else {
                    $this->warn("Could not get dimensions for: {$item->file_name}");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$item->file_name}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("✓ Updated: {$updated}");
        if ($failed > 0) {
            $this->warn("✗ Failed: {$failed}");
        }

        return 0;
    }
}
