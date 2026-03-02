<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Common;

class MigrationRegistry
{
    /**
     * @var array<Migration>
     */
    private $migrations = [];

    public function __construct(private MigrationRepository $repository)
    {
    }

    public function registerMigration(
        string $pluginName,
        string $pluginVersion,
        int $migrationOrder,
        string $migrationClassName
    ): void {
        $this->migrations[] = new Migration(
            $pluginName,
            $pluginVersion,
            $migrationOrder,
            $migrationClassName
        );
    }

    /**
     * @return array<string>
     */
    public function resolveMigrationsForMigrate(string $pluginName): array
    {
        $migrations = $this->extractMigrationsForPlugin($pluginName);
        $migrations = $this->sortMigrationsByOrderOfExecution($migrations);
        $migrations = $this->filterOutAlreadyExecutedMigrations($migrations);
        return $this->extractMigrationClassNames($migrations);
    }

    /**
     * @return array<string>
     */
    public function resolveMigrationsForRollback(string $pluginName): array
    {
        $migrations = $this->extractMigrationsForPlugin($pluginName);
        $migrations = $this->sortMigrationsByOrderOfExecution($migrations);
        $migrations = $this->filterOutNotExecutedMigrations($migrations);
        $migrations = $this->reverseMigrationsOrder($migrations);
        return $this->extractMigrationClassNames($migrations);
    }

    /**
     * @return array<Migration>
     */
    private function extractMigrationsForPlugin(string $pluginName): array
    {
        return array_filter($this->migrations, function ($migration) use ($pluginName) {
            return $migration->pluginName === $pluginName;
        });
    }

    /**
     * @param array<Migration> $migrations
     * @return array<Migration>
     */
    private function sortMigrationsByOrderOfExecution(array $migrations): array
    {
        usort($migrations, function ($a, $b) {
            if (version_compare($a->pluginVersion, $b->pluginVersion, "=")) {
                return $a->migrationOrder - $b->migrationOrder;
            } else {
                return version_compare($a->pluginVersion, $b->pluginVersion, "<") ? -1 : 1;
            }
        });

        return $migrations;
    }

    /**
     * @param array<Migration> $migrations
     * @return array<Migration>
     */
    private function filterOutAlreadyExecutedMigrations(array $migrations): array
    {
        return array_filter($migrations, function ($migration) {
            return $this->repository->existsMigration($migration->pluginName, $migration->className) === false;
        });
    }

    /**
     * @param array<Migration> $migrations
     * @return array<Migration>
     */
    private function filterOutNotExecutedMigrations(array $migrations): array
    {
        return array_filter($migrations, function ($migration) {
            return $this->repository->existsMigration($migration->pluginName, $migration->className) === true;
        });
    }

    /**
     * @param array<Migration> $migrations
     * @return array<Migration>
     */
    private function reverseMigrationsOrder(array $migrations): array
    {
        return array_reverse($migrations);
    }

    /**
     * @param array<Migration> $migrations
     * @return array<string>
     */
    private function extractMigrationClassNames(array $migrations): array
    {
        return array_map(function ($migration) {
            return $migration->className;
        }, $migrations);
    }
}
