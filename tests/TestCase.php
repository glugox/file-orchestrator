<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Support\LocalFilesystem;
use Tests\Support\TestPaths;

/**
 * Provides a minimal application container for exercising the package without
 * requiring Orchestra Testbench or a full Laravel install.
 */
abstract class TestCase extends BaseTestCase
{
    protected LocalFilesystem $filesystem;

    protected string $basePath;

    protected string $backupPath;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    protected function tearDown(): void
    {
        $this->filesystem->deleteDirectory($this->basePath);
        $this->filesystem->deleteDirectory($this->backupPath);

        TestPaths::clear();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }
}
