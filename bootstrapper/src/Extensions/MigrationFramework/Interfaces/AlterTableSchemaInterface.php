<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

interface AlterTableSchemaInterface
{
    public function addColumn(string $name, string $type): ColumnSchemaInterface;
    public function renameColumn(string $oldName, string $newName): void;
    public function alterColumn(string $name): ColumnSchemaInterface;
    public function dropColumn(string $name): void;
    /**
     * @param array<string>|string $columns
     */
    public function addForeign(array|string $columns): ForeignKeySchemaInterface;
    /**
     * @param array<string>|string $columns
     */
    public function dropForeign(array|string $columns): void;
    /**
     * @param array<string>|string $columns
     */
    public function addPrimary(array|string $columns): void;
    public function dropPrimary(): void;
}
