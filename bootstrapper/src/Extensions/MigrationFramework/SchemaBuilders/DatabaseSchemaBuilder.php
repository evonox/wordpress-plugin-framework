<?php

namespace __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders;

use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\DatabaseSchemaInterface;
use Closure;

/**
 * DatabaseSchemaBuilder - this PHP class offers methods to CREATE, ALTER and DROP tables
 */
class DatabaseSchemaBuilder implements DatabaseSchemaInterface
{
    /**
     * @var array<SQLQueryBuilder>
     */
    private array $sqlQueries = [];

    /**
     * CREATE TABLE Command
     */
    public function create(string $tableName, Closure $closureTableSchema): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("CREATE TABLE");
        $builder->identifier($tableName);
        $builder->keyword("(");

        $schemaBuilder = new CreateTableSchemaBuilder();
        call_user_func($closureTableSchema, $schemaBuilder);
        $schemaBuilder->buildSQL($builder);

        $builder->keyword(")");
        $builder->finalize();

        $this->sqlQueries[] = $builder;

        // Process Foreign Keys
        while ($schemaBuilder->hasForeignKey()) {
            $builder = new SQLQueryBuilder();
            $builder->keyword("ALTER TABLE")->identifier($tableName);
            $schemaBuilder->buildForeignKeySQL($builder);
            $builder->finalize();
            $this->sqlQueries[] = $builder;
        }
    }

    /**
     * ALTER TABLE Command
     */
    public function alter(string $tableName, \Closure $closureTableSchema): void
    {
        $schemaBuilder = new AlterTableSchemaBuilder($tableName);
        call_user_func($closureTableSchema, $schemaBuilder);
        $schemaBuilder->buildSQL();
        $this->sqlQueries = array_merge($this->sqlQueries, $schemaBuilder->getListOfQueries());
    }

    /**
     * RENAME TABLE Command
     */
    public function rename(string $oldName, string $newName): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("RENAME TABLE");
        $builder->identifier($oldName);
        $builder->keyword("TO");
        $builder->identifier($newName);
        $builder->finalize();

        $this->sqlQueries[] = $builder;
    }

    /**
     * DROP TABLE Command
     */
    public function drop(string $tableName): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("DROP TABLE");
        $builder->identifier($tableName);
        $builder->finalize();

        $this->sqlQueries[] = $builder;
    }

    /**
     * DROP IF EXISTS TABLE Command
     */
    public function dropIfExists(string $tableName): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("DROP TABLE IF EXISTS");
        $builder->identifier($tableName);
        $builder->finalize();

        $this->sqlQueries[] = $builder;
    }

    /**
     * SQL Query Getters
     */
    public function getQueryCount(): int
    {
        return count($this->sqlQueries);
    }

    public function getQueryAt(int $index): string
    {
        return $this->sqlQueries[$index]->getSQL();
    }
}
