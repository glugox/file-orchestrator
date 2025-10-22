<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

use Illuminate\Support\Facades\File;

class ManifestManager
{
    private ?string $currentBatchName = null;

    public function __construct(
        private readonly string $manifestDir
    ) {
        File::ensureDirectoryExists($manifestDir);
    }

    public function startBatch(string $batchName): void
    {
        $this->currentBatchName = $batchName;
        $path = $this->manifestDir . '/' . $batchName . '.json';
        if (!File::exists($path)) {
            File::put($path, json_encode([
                'batch'     => $batchName,
                'timestamp' => time(),
                'actions'   => []
            ], JSON_PRETTY_PRINT));
        }
    }

    public function recordFile(string $filePath, string $action, ?string $marker = null): void
    {
        if ($this->currentBatchName === null) {
            throw new \RuntimeException("No batch started.");
        }

        $path = $this->manifestDir . '/' . $this->currentBatchName . '.json';
        $data = json_decode(File::get($path), true);
        $data['actions'][] = [
            'file'   => $filePath,
            'action' => $action,
            'marker' => $marker,
            'time'   => time()
        ];
        File::put($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getBatch(string $batchName): array
    {
        $path = $this->manifestDir . '/' . $batchName . '.json';
        if (!File::exists($path)) {
            throw new \RuntimeException("Batch manifest not found: {$batchName}");
        }
        return json_decode(File::get($path), true)['actions'];
    }
}
