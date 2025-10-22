<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

use FilesystemIterator;
use Illuminate\Support\Facades\File;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FullCopySnapshotStrategy implements SnapshotStrategy
{
    private const EXCLUDED_SEGMENTS = [
        '/vendor/',
        '/storage/',
        '/.orchestrator/',
    ];

    public function __construct(
        private readonly string $backupPath
    ) {}

    public function save(string $name): void
    {
        $target = $this->backupPath . '/' . $name;

        if (File::exists($target)) {
            File::deleteDirectory($target);
        }

        File::ensureDirectoryExists($target);

        $source = base_path();
        $directory = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);
        $filter = new RecursiveCallbackFilterIterator($directory, function ($current) {
            $path = $this->normalizePath($current->getPathname());

            foreach (self::EXCLUDED_SEGMENTS as $segment) {
                if (str_contains($path, $segment)) {
                    return false;
                }
            }

            return true;
        });

        $iterator = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $path = $item->getPathname();
            $relative = ltrim(str_replace($source, '', $path), DIRECTORY_SEPARATOR);
            $destination = $target . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                File::ensureDirectoryExists($destination);
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($path, $destination);
        }
    }

    public function restore(string $name): void
    {
        $target = $this->backupPath . '/' . $name;
        if (!File::exists($target)) {
            throw new \RuntimeException("Snapshot {$name} not found.");
        }
        File::copyDirectory($target, base_path());
    }

    private function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
