<?php

declare(strict_types=1);

use Glugox\FileOrchestrator\Services\FullCopySnapshotStrategy;
use Illuminate\Support\Facades\File;

it('saves snapshots while excluding framework directories', function () {
    File::ensureDirectoryExists($this->basePath . '/app');
    file_put_contents($this->basePath . '/app/keep.txt', 'keep');

    File::ensureDirectoryExists($this->basePath . '/nested/vendor/library');
    file_put_contents($this->basePath . '/nested/vendor/library/skip.txt', 'skip');

    File::ensureDirectoryExists($this->basePath . '/storage/cache');
    file_put_contents($this->basePath . '/storage/cache/cache.txt', 'cache');

    File::ensureDirectoryExists($this->basePath . '/.orchestrator/meta');
    file_put_contents($this->basePath . '/.orchestrator/meta/state.json', '{}');

    File::ensureDirectoryExists($this->basePath . '/app/vendorized');
    file_put_contents($this->basePath . '/app/vendorized/keep.txt', 'keep');

    $strategy = new FullCopySnapshotStrategy($this->backupPath);
    $strategy->save('snapshot');

    expect($this->backupPath . '/snapshot/app/keep.txt')->toBeFile();
    expect($this->backupPath . '/snapshot/nested/vendor')->not->toBeDirectory();
    expect($this->backupPath . '/snapshot/storage')->not->toBeDirectory();
    expect($this->backupPath . '/snapshot/.orchestrator')->not->toBeDirectory();
    expect($this->backupPath . '/snapshot/app/vendorized/keep.txt')->toBeFile();
});

it('removes stale snapshot data before saving', function () {
    $snapshot = $this->backupPath . '/stale';
    $this->filesystem->ensureDirectoryExists($snapshot);
    file_put_contents($snapshot . '/stale.txt', 'outdated');

    File::ensureDirectoryExists($this->basePath . '/app');
    file_put_contents($this->basePath . '/app/fresh.txt', 'fresh');

    $strategy = new FullCopySnapshotStrategy($this->backupPath);
    $strategy->save('stale');

    expect($snapshot . '/stale.txt')->not->toBeFile();
    expect($snapshot . '/app/fresh.txt')->toBeFile();
});

it('restores snapshot contents into the base path', function () {
    $snapshotName = 'restore';
    $snapshotDir = $this->backupPath . '/' . $snapshotName;

    $this->filesystem->ensureDirectoryExists($snapshotDir . '/app');
    file_put_contents($snapshotDir . '/app/config.php', 'restored');

    File::ensureDirectoryExists($this->basePath . '/app');
    file_put_contents($this->basePath . '/app/config.php', 'outdated');

    $strategy = new FullCopySnapshotStrategy($this->backupPath);
    $strategy->restore($snapshotName);

    expect($this->basePath . '/app/config.php')->toBeFile();
    expect(file_get_contents($this->basePath . '/app/config.php'))->toBe('restored');
});

it('throws when attempting to restore a missing snapshot', function () {
    $strategy = new FullCopySnapshotStrategy($this->backupPath);

    $strategy->restore('missing');
})->throws(RuntimeException::class, 'Snapshot missing not found.');
