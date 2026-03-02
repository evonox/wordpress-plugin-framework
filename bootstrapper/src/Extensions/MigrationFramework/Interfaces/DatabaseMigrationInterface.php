<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

interface DatabaseMigrationInterface
{
    public function up(DatabaseSchemaInterface $schema): void;
    public function down(DatabaseSchemaInterface $schema): void;
}
