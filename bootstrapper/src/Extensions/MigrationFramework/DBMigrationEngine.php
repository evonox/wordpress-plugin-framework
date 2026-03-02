<?php

namespace __PLUGIN__\Extensions\MigrationFramework;

use __PLUGIN__\Extensions\MigrationFramework\Attributes\MigrationOrder;
use __PLUGIN__\Extensions\MigrationFramework\Attributes\PluginName;
use __PLUGIN__\Extensions\MigrationFramework\Attributes\PluginVersion;
use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRegistry;
use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRepository;
use __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders\DatabaseSchemaBuilder;
use Exception;
use ReflectionClass;

class DBMigrationEngine
{
    public function __construct(
        private MigrationRegistry $registry,
        private MigrationRepository $repository
    ) {
    }

    public function migrate(string $pluginName): void
    {
        $migrations = $this->registry->resolveMigrationsForMigrate($pluginName);
        $this->migrateUp($pluginName, $migrations);
    }

    public function rollback(string $pluginName): void
    {
        $migrations = $this->registry->resolveMigrationsForRollback($pluginName);
        $this->migrateDown($pluginName, $migrations);
    }

    public function defineMigration(string $className): void
    {
        $pluginName = $this->getAttributeValue($className, PluginName::class);
        $pluginVersion = $this->getAttributeValue($className, PluginVersion::class);
        $migrationOrder = $this->getAttributeValue($className, MigrationOrder::class);

        if ($pluginName === false || is_string($pluginName) === false) {
            throw new Exception(
                "Migration '$className' is missing 'PluginName' attribute or has invalid value type."
            );
        }
        if ($pluginVersion === false || is_string($pluginVersion) === false) {
            throw new Exception(
                "Migration '$className' is missing 'PluginVersion' attribute or has invalid value type."
            );
        }
        if ($migrationOrder === false || is_int($migrationOrder) === false) {
            throw new Exception(
                "Migration '$className' is missing 'MigrationOrder' attribute or has invalid value type."
            );
        }

        $this->registry->registerMigration($pluginName, $pluginVersion, $migrationOrder, $className);
    }

    private function getAttributeValue(string $className, string $attributeName): string|int|false
    {
        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->name === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance->value;
            }
        }

        return false;
    }

    public function scanMigrations(string $globPattern): void
    {
        foreach (glob($globPattern) as $filePath) {
            include_once($filePath);
        }
    }

    /**
     * @param array<string> $migrations
     */
    private function migrateUp(string $pluginName, array $migrations): void
    {
        global $wpdb;

        foreach ($migrations as $migrationName) {
            $migrationInstance = new $migrationName();
            $schemaBuilder = new DatabaseSchemaBuilder();
            $migrationInstance->up($schemaBuilder);

            for ($i = 0; $i < $schemaBuilder->getQueryCount(); $i++) {
                $sqlQuery = $schemaBuilder->getQueryAt($i);
                $wpdb->query($sqlQuery);
            }

            $this->repository->saveMigration($pluginName, $migrationName);
        }
    }

    /**
     * @param array<string> $migrations
     */
    private function migrateDown(string $pluginName, array $migrations): void
    {
        global $wpdb;

        foreach ($migrations as $migrationName) {
            $migrationInstance = new $migrationName();
            $schemaBuilder = new DatabaseSchemaBuilder();
            $migrationInstance->down($schemaBuilder);

            for ($i = 0; $i < $schemaBuilder->getQueryCount(); $i++) {
                $sqlQuery = $schemaBuilder->getQueryAt($i);
                $wpdb->query($sqlQuery);
            }

            $this->repository->removeMigration($pluginName, $migrationName);
        }
    }
}
