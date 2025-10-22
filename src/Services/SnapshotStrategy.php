<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

interface SnapshotStrategy
{
    public function save(string $name): void;
    public function restore(string $name): void;
}
