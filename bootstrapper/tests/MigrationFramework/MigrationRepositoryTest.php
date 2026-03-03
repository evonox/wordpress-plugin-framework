<?php

namespace Tests\MigrationFramework;

use __PLUGIN__\Framework\DI\Container;
use __PLUGIN__\Framework\Helpers\ExtensionsHelper;
use PHPUnit\Framework\TestCase;
use Tests\Helper\MigrationB;
use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRepository;

class MigrationRepositoryTest extends TestCase
{
    private MigrationRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = Container::get()->resolve(MigrationRepository::class);
    }

    public function testIfMigrationDoesNotExist(): void
    {
        $this->repository->migrateUp();
        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertFalse($exists);
        $this->repository->migrateDown();
    }

    public function testIfMigrationIsAdded(): void
    {
        $this->repository->migrateUp();
        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertFalse($exists);
        $this->repository->saveMigration("PluginA", MigrationB::class);
        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertTrue($exists);
        $this->repository->migrateDown();
    }

    public function testIfMigrationIsRemoved(): void
    {
        $this->repository->migrateUp();

        $this->repository->saveMigration("PluginA", MigrationB::class);
        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertTrue($exists);

        $this->repository->removeMigration("PluginA", MigrationB::class);
        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertFalse($exists);


        $this->repository->migrateDown();
    }

    public function testIfMigrationsAreIdentifiedByPluginName(): void
    {
        $this->repository->migrateUp();
        $this->repository->saveMigration("PluginA", MigrationB::class);

        $exists = $this->repository->existsMigration("PluginA", MigrationB::class);
        $this->assertTrue($exists);

        $exists = $this->repository->existsMigration("PluginB", MigrationB::class);
        $this->assertFalse($exists);

        $this->repository->migrateDown();
    }

    public function testIfThrowsExceptionOnDuplicateSave(): void
    {
        $this->repository->migrateUp();
        $this->repository->saveMigration("PluginA", MigrationB::class);

        try {
            $this->expectException(\Exception::class);
            $this->repository->saveMigration("PluginA", MigrationB::class);
        } finally {
            $this->repository->migrateDown();
        }
    }

    public function testIfThrowsExceptionOnDuplicateDelete(): void
    {
        $this->repository->migrateUp();

        try {
            $this->expectException(\Exception::class);
            $this->repository->removeMigration("PluginA", MigrationB::class);
        } finally {
            $this->repository->migrateDown();
        }
    }
}
