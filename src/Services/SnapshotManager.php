<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

class SnapshotManager
{
    public function __construct(
        private readonly SnapshotStrategy $strategy
    ) {}

    public function snapshot(string $name = null): string
    {
        $name = $name ?? date('Ymd_His');
        $this->strategy->save($name);
        return $name;
    }

    public function restore(string $name): void
    {
        $this->strategy->restore($name);
    }
}
