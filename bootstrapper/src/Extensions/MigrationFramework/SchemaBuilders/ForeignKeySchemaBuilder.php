<?php

namespace __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders;

use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ForeignKeySchemaInterface;

class ForeignKeySchemaBuilder implements ForeignKeySchemaInterface
{
    /**
     * @var array<string>
     */
    private array $columns = [];
    /**
     * @var array<string>
     */
    private array $referencedColumns = [];
    private string $referencedTable;

    /**
     * @param array<string>|string $columns
     */
    public function __construct(string|array $columns)
    {
        if (is_string($columns)) {
            $this->columns = [$columns];
        } else {
            $this->columns = $columns;
        }
    }

    /**
     * @param array<string>|string $columns
     */
    public function references(string|array $columns): self
    {
        if (is_string($columns)) {
            $this->referencedColumns = [$columns];
        } else {
            $this->referencedColumns = $columns;
        }
        return $this;
    }

    public function on(string $tableName): self
    {
        $this->referencedTable = $tableName;
        return $this;
    }

    public function buildSQL(SQLQueryBuilder $builder): void
    {
        $builder->keyword("(")->identifierList($this->columns)->keyword(")");
        $builder->keyword("REFERENCES")->identifier($this->referencedTable);
        $builder->keyword("(")->identifierList($this->referencedColumns)->keyword(")");
    }
}
