<?php

declare(strict_types=1);

namespace Tests\Support;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class LocalFilesystem
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        if (!is_dir($directory)) {
            return true;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $pathname = $item->getPathname();

            if ($item->isDir()) {
                if (!@rmdir($pathname)) {
                    throw new RuntimeException("Unable to delete directory: {$pathname}");
                }

                continue;
            }

            if (!@unlink($pathname)) {
                throw new RuntimeException("Unable to delete file: {$pathname}");
            }
        }

        if (!$preserve && !@rmdir($directory)) {
            throw new RuntimeException("Unable to delete directory: {$directory}");
        }

        return true;
    }

    public function ensureDirectoryExists(string $path, int $mode = 0777, bool $recursive = true): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!@mkdir($path, $mode, $recursive) && !is_dir($path)) {
            throw new RuntimeException("Unable to create directory: {$path}");
        }
    }

    public function copy(string $from, string $to): bool
    {
        $this->ensureDirectoryExists(dirname($to));

        if (!@copy($from, $to)) {
            throw new RuntimeException("Unable to copy file to {$to}");
        }

        return true;
    }

    public function copyDirectory(string $from, string $to): bool
    {
        if (!is_dir($from)) {
            return false;
        }

        $this->ensureDirectoryExists($to);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $targetPath = $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                $this->ensureDirectoryExists($targetPath);
                continue;
            }

            $this->copy($item->getPathname(), $targetPath);
        }

        return true;
    }
}
