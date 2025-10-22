<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator;

use Illuminate\Support\ServiceProvider;
use Glugox\FileOrchestrator\Services\FileManager;
use Glugox\FileOrchestrator\Services\ManifestManager;
use Glugox\FileOrchestrator\Services\SnapshotManager;
use Glugox\FileOrchestrator\Services\FullCopySnapshotStrategy;

class FileOrchestratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ManifestManager::class, function (): ManifestManager {
            return new ManifestManager(base_path('.orchestrator/manifests'));
        });

        $this->app->singleton(FileManager::class, function ($app): FileManager {
            return new FileManager($app->make(ManifestManager::class));
        });

        $this->app->singleton(SnapshotManager::class, function (): SnapshotManager {
            $backupPath = base_path('.orchestrator/backups');
            $strategy = new FullCopySnapshotStrategy($backupPath);
            return new SnapshotManager($strategy);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Glugox\FileOrchestrator\Console\SnapshotCommand::class,
                \Glugox\FileOrchestrator\Console\UndoCommand::class,
                \Glugox\FileOrchestrator\Console\UndoBatchCommand::class,
            ]);
        }
    }
}
