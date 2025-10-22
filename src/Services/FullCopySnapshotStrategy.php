<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

use Illuminate\Support\Facades\File;

class FullCopySnapshotStrategy implements SnapshotStrategy
{
    public function __construct(
        private readonly string $backupPath
    ) {}

    public function save(string $name): void
    {
        $target = $this->backupPath . '/' . $name;
        File::ensureDirectoryExists($target);

        File::copyDirectory(base_path(), $target, function ($file) {
            return !str_contains($file, '/vendor/') && !str_contains($file, '/storage/');
        });
    }

    public function restore(string $name): void
    {
        $target = $this->backupPath . '/' . $name;
        if (!File::exists($target)) {
            throw new \RuntimeException("Snapshot {$name} not found.");
        }
        File::copyDirectory($target, base_path());
    }
}
