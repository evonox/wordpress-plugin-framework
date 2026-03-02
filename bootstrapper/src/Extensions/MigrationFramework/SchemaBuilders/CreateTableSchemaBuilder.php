<?php

namespace __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders;

use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ColumnSchemaInterface;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\CreateTableSchemaInterface;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ForeignKeySchemaInterface;

class CreateTableSchemaBuilder implements CreateTableSchemaInterface
{
    /**
     * @var array<ColumnSchemaBuilder>
     */
    private array $columnBuilders = [];
    /**
     * @var array<ForeignKeySchemaBuilder>
     */
    private array $foreignKeyBuilders = [];


    public function column(string $name, string $type): ColumnSchemaInterface
    {
        $builder = new ColumnSchemaBuilder($name);
        $builder->type($type);
        $this->columnBuilders[] = $builder;
        return $builder;
    }

    /**
     * @param array<string>|string $columns
     */
    public function foreign(array|string $columns): ForeignKeySchemaInterface
    {
        $builder = new ForeignKeySchemaBuilder($columns);
        $this->foreignKeyBuilders[] = $builder;
        return $builder;
    }

    public function buildSQL(SQLQueryBuilder $builder): void
    {
        $primaryKeys = [];

        $builder->list($this->columnBuilders, ",", function ($builder, $columnBuilder) use (&$primaryKeys) {
            $columnBuilder->buildSQL($builder);
            if ($columnBuilder->isPrimaryKey()) {
                $primaryKeys[] = $columnBuilder->getColumnName();
            }
        });

        if (count($primaryKeys) > 0) {
            $builder->keyword(", PRIMARY KEY (")->identifierList($primaryKeys)->keyword(")");
        }
    }

    public function hasForeignKey(): bool
    {
        return count($this->foreignKeyBuilders) > 0;
    }

    public function buildForeignKeySQL(SQLQueryBuilder $builder): void
    {
        $schemaBuilder = array_shift($this->foreignKeyBuilders);
        $builder->keyword("ADD FOREIGN KEY");
        $schemaBuilder->buildSQL($builder);
    }
}
