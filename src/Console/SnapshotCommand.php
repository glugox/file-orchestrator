<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Console;

use Illuminate\Console\Command;
use Glugox\FileOrchestrator\Services\SnapshotManager;

class SnapshotCommand extends Command
{
    protected $signature = 'orchestrator:snapshot {name?}';
    protected $description = 'Take a snapshot of the current project files';

    public function handle(SnapshotManager $snapshots): void
    {
        $name = $snapshots->snapshot($this->argument('name'));
        $this->info("Snapshot created: {$name}");
    }
}
