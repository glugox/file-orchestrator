<?php

declare(strict_types=1);

namespace Glugox\FileOrchestrator\Services;

use Illuminate\Support\Facades\File;

class FileManager
{
    public function __construct(
        private readonly ManifestManager $manifest
    ) {}

    /**
     * Inject code after the last occurrence of a given regex pattern.
     *
     * @param string $file
     * @param string $pattern
     * @param string $content
     * @param string $batchName
     */
    public function injectAfterPattern(string $file, string $pattern, string $content, string $batchName): void
    {
        $path = base_path($file);
        $exists = File::exists($path);
        $original = $exists ? File::get($path) : '';

        if (preg_match_all($pattern, $original, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($matches[0]);
            $pos = $lastMatch[1] + strlen($lastMatch[0]);

            $new = substr($original, 0, $pos) . PHP_EOL
                . $content . PHP_EOL
                . substr($original, $pos);
        } else {
            $new = $original . PHP_EOL . $content . PHP_EOL;
        }

        File::put($path, $new);

        $this->manifest->startBatch($batchName);
        $this->manifest->recordFile($file, $exists ? 'modified' : 'created');
    }
}
