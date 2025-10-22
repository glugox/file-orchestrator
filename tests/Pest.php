<?php

declare(strict_types=1);

require_once __DIR__ . '/Support/TestPaths.php';
require_once __DIR__ . '/Support/LocalFilesystem.php';
require_once __DIR__ . '/TestCase.php';

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return Tests\Support\TestPaths::basePath($path);
    }
}

uses(Tests\TestCase::class)->in(__DIR__ . '/Feature');
