<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Common;

use __PLUGIN__\Framework\Attributes\Inject;

class MigrationRepository
{
    #[Inject("PluginPrefix")]
    private string $pluginPrefix = "";

    public function migrateUp(): void
    {
        global $wpdb;
        $tableName = $this->getMigrationTableName();

        $sql = "CREATE TABLE `$tableName` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `plugin_name` VARCHAR(500) NOT NULL,
            `migration_name` VARCHAR(500) NOT NULL,
            PRIMARY KEY (`id`)
        ); ";

        $wpdb->query($sql);
    }

    public function migrateDown(): void
    {
        global $wpdb;
        $tableName = $this->getMigrationTableName();
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        $wpdb->query($sql);
    }

    public function existsMigration(string $pluginName, string $className): bool
    {
        global $wpdb;

        $tableName = $this->getMigrationTableName();
        $migrationName = $this->stripNamespaceFromClassName($className);

        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM `$tableName` WHERE `plugin_name` = %s AND `migration_name` = %s ;",
            $pluginName,
            $migrationName
        );
        $count = $wpdb->get_var($sql);

        return $count > 0;
    }

    public function saveMigration(string $pluginName, string $className): void
    {
        global $wpdb;
        if ($this->existsMigration($pluginName, $className)) {
            throw new \Exception("Migration '$className' is already present in the database. Cannot insert again.");
        }

        $tableName = $this->getMigrationTableName();
        $migrationName = $this->stripNamespaceFromClassName($className);

        $sql = $wpdb->prepare(
            "INSERT INTO `$tableName` (`plugin_name`, `migration_name`) VALUES(%s, %s) ;",
            $pluginName,
            $migrationName
        );
        $wpdb->query($sql);
    }

    public function removeMigration(string $pluginName, string $className): void
    {
        global $wpdb;
        if ($this->existsMigration($pluginName, $className) === false) {
            throw new \Exception("Migration '$className' does not exist in the database. Cannot remove it.");
        }

        $tableName = $this->getMigrationTableName();
        $migrationName = $this->stripNamespaceFromClassName($className);

        $sql = $wpdb->prepare(
            "DELETE FROM `$tableName` WHERE `plugin_name` = %s AND `migration_name` = %s ;",
            $pluginName,
            $migrationName
        );
        $wpdb->query($sql);
    }

    private function stripNamespaceFromClassName(string $className): string
    {
        $reflection = new \ReflectionClass($className);
        $shortName = $reflection->getShortName();
        return $shortName;
    }

    private function getMigrationTableName(): string
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->pluginPrefix . '_migrations';
        return $table_name;
    }
}
