<?php

namespace __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders;

use __PLUGIN__\Extensions\MigrationFramework\Common\SchemaQuery;
use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\AlterTableSchemaInterface;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ColumnSchemaInterface;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ForeignKeySchemaInterface;

class AlterTableSchemaBuilder implements AlterTableSchemaInterface
{
    private string $tableName;

    /**
     * @var array<SQLQueryBuilder>
     */
    private array $sqlBuilders = [];
    /**
     * Summary of schemas
     * @var array<ColumnSchemaBuilder|ForeignKeySchemaBuilder>
     */
    private $schemas = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function addColumn(string $name, string $type): ColumnSchemaInterface
    {
        $columnSchema = new ColumnSchemaBuilder($name);
        $columnSchema->type($type);
        $this->schemas[] = $columnSchema;

        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("ADD COLUMN");
        $this->sqlBuilders[] = $builder;

        return $columnSchema;
    }

    public function renameColumn(string $oldName, string $newName): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("RENAME COLUMN")
            ->identifier($oldName)->keyword("TO")->identifier($newName)->finalize();

        $this->schemas[] = $builder;
        $this->sqlBuilders[] = $builder;
    }

    public function alterColumn(string $name): ColumnSchemaInterface
    {
        $columnSchema = new ColumnSchemaBuilder($name);
        $this->schemas[] = $columnSchema;

        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("MODIFY COLUMN");
        $this->sqlBuilders[] = $builder;

        return $columnSchema;
    }

    public function dropColumn(string $name): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("DROP COLUMN")
            ->identifier($name)->finalize();

        $this->schemas[] = $builder;
        $this->sqlBuilders[] = $builder;
    }

    /**
     * @param array<string>|string $columns
     */
    public function addForeign(array|string $columns): ForeignKeySchemaInterface
    {
        $foreignKeySchema = new ForeignKeySchemaBuilder($columns);
        $this->schemas[] = $foreignKeySchema;

        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("ADD FOREIGN KEY");
        $this->sqlBuilders[] = $builder;

        return $foreignKeySchema;
    }

    public function dropForeign(array|string $columns): void
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $constraintName = SchemaQuery::getForeignKeyName($this->tableName, $columns);

        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("DROP FOREIGN KEY")
            ->identifier($constraintName)->finalize();

        $this->schemas[] = $builder;
        $this->sqlBuilders[] = $builder;
    }

    public function addPrimary(array|string $columns): void
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("ADD PRIMARY KEY (")
            ->identifierList($columns)->keyword(")")->finalize();

        $this->schemas[] = $builder;
        $this->sqlBuilders[] = $builder;
    }

    public function dropPrimary(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("ALTER TABLE")->identifier($this->tableName)->keyword("DROP PRIMARY KEY")->finalize();
        $this->schemas[] = $builder;
        $this->sqlBuilders[] = $builder;
    }

    public function buildSQL(): void
    {
        $count = count($this->schemas);

        for ($i = 0; $i < $count; $i++) {
            $schema = $this->schemas[$i];
            $builder = $this->sqlBuilders[$i];

            if ($schema instanceof ColumnSchemaBuilder) {
                $schema->buildSQL($builder, true);
                $builder->finalize();
            } elseif ($schema instanceof ForeignKeySchemaBuilder) {
                $schema->buildSQL($builder);
                $builder->finalize();
            }
        }
    }

    /**
     * @return SQLQueryBuilder[]
     */
    public function getListOfQueries(): array
    {
        return $this->sqlBuilders;
    }
}
