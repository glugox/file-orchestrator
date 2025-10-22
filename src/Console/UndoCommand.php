<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Console;

use Illuminate\Console\Command;
use Glugox\FileOrchestrator\Services\SnapshotManager;

class UndoCommand extends Command
{
    protected $signature = 'orchestrator:undo {name}';
    protected $description = 'Restore project from snapshot';

    public function handle(SnapshotManager $snapshots): void
    {
        $snapshots->restore($this->argument('name'));
        $this->info("Restored snapshot: " . $this->argument('name'));
    }
}
