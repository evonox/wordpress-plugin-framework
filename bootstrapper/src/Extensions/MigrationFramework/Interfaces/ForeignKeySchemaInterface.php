<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

interface ForeignKeySchemaInterface
{
    /**
     * @param array<string> $columns
     */
    public function references(array $columns): self;
    public function on(string $tableName): self;
}
