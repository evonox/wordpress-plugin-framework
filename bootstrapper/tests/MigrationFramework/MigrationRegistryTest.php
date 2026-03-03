<?php

namespace Tests\MigrationFramework;

use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRepository;
use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRegistry;
use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;
use Tests\Helper\MigrationA;
use Tests\Helper\MigrationB;
use Tests\Helper\MigrationC;
use Tests\Helper\MigrationD;

class MigrationRegistryTest extends TestCase
{
    private MigrationRepository $repository;
    private MigrationRegistry $registry;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = Container::get()->resolve(MigrationRepository::class);
        $this->registry = Container::get()->resolve(MigrationRegistry::class);
    }

    public function testIfMMigrationsAreExtractedForGivenPlugin(): void
    {
        $this->repository->migrateUp();

        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationB::class);
        $this->registry->registerMigration("PluginB", "1.0.0", 1, MigrationC::class);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginA");
        $this->assertEquals($migrations, [MigrationA::class, MigrationB::class]);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginB");
        $this->assertEquals($migrations, [MigrationC::class]);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginC");
        $this->assertEquals($migrations, []);

        $this->repository->migrateDown();
    }

    public function testIfMigrationsAreSortedByPluginVersion(): void
    {
        $this->repository->migrateUp();

        $this->registry->registerMigration("PluginA", "2.1.0", 1, MigrationD::class);
        $this->registry->registerMigration("PluginA", "2.0.0", 1, MigrationC::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationB::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginA");
        $this->assertEquals($migrations, [MigrationA::class, MigrationB::class, MigrationC::class, MigrationD::class]);

        $this->repository->migrateDown();
    }

    public function testIfMigrationsAreSortedByPluginVersionAndMigrationOrder(): void
    {
        $this->repository->migrateUp();

        $this->registry->registerMigration("PluginA", "1.0.0", 3, MigrationD::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationC::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 2, MigrationB::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginA");
        $this->assertEquals($migrations, [MigrationA::class, MigrationB::class, MigrationD::class, MigrationC::class]);

        $this->repository->migrateDown();
    }

    public function testIfAlreadyExecutedMigrationsAreFilteredOut(): void
    {
        $this->repository->migrateUp();

        $this->repository->saveMigration("PluginA", MigrationB::class);
        $this->repository->saveMigration("PluginB", MigrationA::class);

        $this->registry->registerMigration("PluginA", "1.0.0", 3, MigrationD::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationC::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 2, MigrationB::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);

        $migrations = $this->registry->resolveMigrationsForMigrate("PluginA");
        $this->assertEquals(array_values($migrations), [MigrationA::class, MigrationD::class, MigrationC::class]);

        $this->repository->migrateDown();
    }

    public function testIfNotExecutedMigrationsAreFilteredOut(): void
    {
        $this->repository->migrateUp();

        $this->repository->saveMigration("PluginA", MigrationB::class);
        $this->repository->saveMigration("PluginB", MigrationA::class);

        $this->registry->registerMigration("PluginA", "1.0.0", 3, MigrationD::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationC::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 2, MigrationB::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);

        $migrations = $this->registry->resolveMigrationsForRollback("PluginA");
        $this->assertEquals(array_values($migrations), [MigrationB::class]);

        $this->repository->migrateDown();
    }

    public function testIfMigrationOrderIsReversedForRollbackOperation(): void
    {
        $this->repository->migrateUp();

        $this->repository->saveMigration("PluginA", MigrationB::class);
        $this->repository->saveMigration("PluginA", MigrationA::class);
        $this->repository->saveMigration("PluginA", MigrationC::class);

        $this->registry->registerMigration("PluginA", "1.0.0", 3, MigrationD::class);
        $this->registry->registerMigration("PluginA", "1.0.1", 1, MigrationC::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 2, MigrationB::class);
        $this->registry->registerMigration("PluginA", "1.0.0", 1, MigrationA::class);

        $migrations = $this->registry->resolveMigrationsForRollback("PluginA");
        $this->assertEquals(array_values($migrations), [MigrationC::class, MigrationB::class, MigrationA::class]);

        $this->repository->migrateDown();
    }
}
