<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

final class TestPaths
{
    private static ?string $basePath = null;

    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, DIRECTORY_SEPARATOR);
    }

    public static function basePath(string $path = ''): string
    {
        if (self::$basePath === null) {
            throw new RuntimeException('Test base path has not been initialised.');
        }

        $relative = ltrim($path, DIRECTORY_SEPARATOR);

        return $relative === ''
            ? self::$basePath
            : self::$basePath . DIRECTORY_SEPARATOR . $relative;
    }

    public static function clear(): void
    {
        self::$basePath = null;
    }
}
