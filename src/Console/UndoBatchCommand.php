<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Console;

use Illuminate\Console\Command;
use Glugox\FileOrchestrator\Services\ManifestManager;
use Illuminate\Support\Facades\File;

class UndoBatchCommand extends Command
{
    protected $signature = 'orchestrator:undo:batch {batch}';
    protected $description = 'Undo changes applied in the given batch';

    public function handle(ManifestManager $manifest): void
    {
        $batchName = $this->argument('batch');
        $actions   = $manifest->getBatch($batchName);

        foreach (array_reverse($actions) as $act) {
            $file   = base_path($act['file']);
            $action = $act['action'];

            if ($action === 'created' && File::exists($file)) {
                File::delete($file);
                $this->info("Deleted created file: {$act['file']}");
            } elseif ($action === 'modified') {
                $this->warn("Cannot auto-restore modified file: {$act['file']} (requires snapshot)");
            }
        }

        $this->info("Batch {$batchName} undone.");
    }
}
