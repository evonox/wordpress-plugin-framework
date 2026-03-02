<?php

namespace __PLUGIN__\Extensions\MigrationFramework;

use __PLUGIN__\Extensions\MigrationFramework\Interfaces\DatabaseMigrationInterface;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\DatabaseSchemaInterface;
use __PLUGIN__\Framework\DI\Container;

abstract class MigrationBase implements DatabaseMigrationInterface
{
    abstract public function up(DatabaseSchemaInterface $schema): void;
    abstract public function down(DatabaseSchemaInterface $schema): void;

    public static function register(): void
    {
        Container::get()->resolve(DBMigrationEngine::class)->defineMigration(static::class);
    }
}
