<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

interface CreateTableSchemaInterface
{
    public function column(string $name, string $type): ColumnSchemaInterface;
    /**
     * @param array<string> $columns
     */
    public function foreign(array $columns): ForeignKeySchemaInterface;
}
