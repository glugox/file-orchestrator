<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Tests\Support\LocalFilesystem;
use Tests\Support\TestPaths;

require_once __DIR__ . '/Support/TestPaths.php';
require_once __DIR__ . '/Support/LocalFilesystem.php';

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return TestPaths::basePath($path);
    }
}

uses()
    ->beforeEach(function () {
        $container = new Container();
        $filesystem = new LocalFilesystem();

        $container->instance('files', $filesystem);
        Facade::setFacadeApplication($container);

        $this->filesystem = $filesystem;
        $this->basePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fo-base-' . uniqid('', true);
        $this->backupPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fo-backup-' . uniqid('', true);

        $filesystem->ensureDirectoryExists($this->basePath);
        $filesystem->ensureDirectoryExists($this->backupPath);

        TestPaths::setBasePath($this->basePath);
    })
    ->afterEach(function () {
        $this->filesystem->deleteDirectory($this->basePath);
        $this->filesystem->deleteDirectory($this->backupPath);

        TestPaths::clear();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
    });
