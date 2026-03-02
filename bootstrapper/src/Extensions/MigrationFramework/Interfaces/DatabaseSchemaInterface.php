<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

use Closure;

interface DatabaseSchemaInterface
{
    public function create(string $tableName, Closure $closureTableSchema): void;
    public function alter(string $tableName, Closure $closureTableSchema): void;
    public function rename(string $oldName, string $newName): void;
    public function drop(string $tableName): void;
    public function dropIfExists(string $tableName): void;
}
