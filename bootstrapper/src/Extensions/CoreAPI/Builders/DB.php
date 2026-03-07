<?php

namespace __PLUGIN__\Extensions\CoreAPI\Builders;

use __PLUGIN__\Extensions\CoreAPI\Exceptions\RuntimeApiException;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\DatabaseAPI;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\DatabaseQueryBuilder;
use __PLUGIN__\Framework\DI\Container;
use Exception;
use Throwable;
use Closure;

class DB implements DatabaseQueryBuilder
{
    private SQLQueryBuilder $builder;
    private bool $whereKWAdded = false;
    private bool $orderByKWAdded = false;
    private bool $havingKWAdded = false;

    public static function get(): DatabaseQueryBuilder
    {
        return Container::get()->make(DB::class);
    }

    public function __construct(private DatabaseAPI $databaseAPI)
    {
        $this->builder = new SQLQueryBuilder();
    }

    public function getSQL(): string
    {
        return $this->builder->getSQL();
    }

    /**
     * @param string|array<string> $columns
     */
    public function select(string|array ...$columns): self
    {
        $this->builder->keyword("SELECT");

        $firstIteration = true;
        foreach ($columns as $column) {
            if (! $firstIteration) {
                $this->builder->keyword(",");
            } else {
                $firstIteration = false;
            }

            if (is_string($column)) {
                $this->builder->identifier($column);
            // @phpstan-ignore function.alreadyNarrowedType
            } elseif (is_array($column) && count($column) === 2) {
                $this->builder->identifier($column[0])->keyword("AS")->identifier($column[1]);
            } else {
                throw new RuntimeApiException("Column must be a string or array of two strings.");
            }
        }

        return $this;
    }

    public function from(string $table, ?string $alias = null): self
    {
        $this->builder->keyword("FROM");
        $this->writeTableWithAlias($table, $alias);
        return $this;
    }

    public function join(string $table, ?string $alias = null): self
    {
        $this->builder->keyword("JOIN");
        $this->writeTableWithAlias($table, $alias);
        return $this;
    }

    public function leftJoin(string $table, ?string $alias = null): self
    {
        $this->builder->keyword("LEFT JOIN");
        $this->writeTableWithAlias($table, $alias);
        return $this;
    }

    public function rightJoin(string $table, ?string $alias = null): self
    {
        $this->builder->keyword("RIGHT JOIN");
        $this->writeTableWithAlias($table, $alias);
        return $this;
    }

    private function writeTableWithAlias(string $table, ?string $alias = null): self
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $this->builder->raw($table);
        if ($alias !== null) {
            $this->builder->raw("AS")->identifier($alias);
        }
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    private function writeColumnName(array|string $column): self
    {
        if (is_string($column)) {
            $this->builder->identifier($column);
        // @phpstan-ignore function.alreadyNarrowedType
        } elseif (is_array($column) && count($column) === 2) {
            $this->builder->identifier($column[0])->raw(".")->identifier($column[1]);
        } else {
            throw new RuntimeApiException("Column name must be a string or array of strings");
        }
        return $this;
    }

    public function on(string|array $column1, string|array $column2): self
    {
        $this->builder->keyword("ON");
        $this->writeColumnName($column1);
        $this->builder->raw("=");
        $this->writeColumnName($column2);
        return $this;
    }

    private function ensureWhereClause(): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        return $this;
    }

    public function where(string|array $column, string $operator, mixed $value): self
    {
        $this->ensureWhereClause();
        $this->writeColumnName($column);
        $this->builder->raw($operator)->value($value);
        return $this;
    }

    public function whereNull(string|array $column): self
    {
        $this->ensureWhereClause();
        $this->writeColumnName($column);
        $this->builder->keyword("IS NULL");
        return $this;
    }

    public function whereNotNull(string|array $column): self
    {
        $this->ensureWhereClause();
        $this->writeColumnName($column);
        $this->builder->keyword("IS NOT NULL");
        return $this;
    }

    /**
     * @param string|array<string> $column
     * @param array<mixed> $values
     */
    public function whereIn(string|array $column, array $values): self
    {
        $this->ensureWhereClause();
        $this->writeColumnName($column);
        $this->builder
            ->keyword("IN (")
            ->list($values, ",", function ($builder, $value) {
                $builder->value($value);
            })
            ->raw(")");

        return $this;
    }

    /**
     * @param string|array<string> $column
     * @param array<mixed> $values
     */
    public function whereNotIn(string|array $column, array $values): self
    {
        $this->ensureWhereClause();
        $this->writeColumnName($column);
        $this->builder
            ->keyword("NOT IN (")
            ->list($values, ",", function ($builder, $value) {
                $builder->value($value);
            })
            ->raw(")");

        return $this;
    }

    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function whereExists(Closure $callback): self
    {
        $this->ensureWhereClause();
        $this->builder->keyword("EXISTS")->raw("(");
        $callback($this);
        $this->builder->raw(")");
        return $this;
    }

    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function whereNotExists(Closure $callback): self
    {
        $this->ensureWhereClause();
        $this->builder->keyword("NOT EXISTS")->raw("(");
        $callback($this);
        $this->builder->raw(")");
        return $this;
    }

    public function not(): self
    {
        $this->ensureWhereClause();
        $this->builder->keyword("NOT");
        return $this;
    }

    public function or(): self
    {
        $this->builder->keyword("OR");
        return $this;
    }

    public function and(): self
    {
        $this->builder->keyword("AND");
        return $this;
    }
    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function inParen(Closure $callback): self
    {
        $this->builder->raw("(");
        $callback($this);
        $this->builder->raw(")");
        return $this;
    }

    /**
     * @param string|array<string> $columns
     */
    public function groupBy(string|array ...$columns): self
    {
        $this->builder->keyword("GROUP BY")->list($columns, ",", function ($b, $column) {
            $this->writeColumnName($column);
        });
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    public function orderBy(string|array $column, string $order = "ASC"): self
    {
        if (!$this->orderByKWAdded) {
            $this->builder->keyword("ORDER BY");
            $this->orderByKWAdded = true;
        } else {
            $this->builder->keyword(",");
        }
        $this->writeColumnName($column);
        $this->builder->keyword($order);
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    public function having(string|array $column, string $operator, mixed $value): self
    {
        $this->ensureHavingClause();
        $this->writeColumnName($column);
        $this->builder->raw($operator)->value($value);
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    public function havingNull(string|array $column): self
    {
        $this->ensureHavingClause();
        $this->writeColumnName($column);
        $this->builder->keyword("IS NULL");
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    public function havingNotNull(string|array $column): self
    {
        $this->ensureHavingClause();
        $this->writeColumnName($column);
        $this->builder->keyword("IS NOT NULL");
        return $this;
    }

    private function ensureHavingClause(): void
    {
        if (!$this->havingKWAdded) {
            $this->builder->keyword("HAVING");
            $this->havingKWAdded = true;
        }
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->builder->keyword("LIMIT")->value($limit)->keyword("OFFSET")->value($offset);
        return $this;
    }

    public function count(): int
    {
        $sql = $this->builder->getSQL();
        $this->builder = new SQLQueryBuilder();
        $this->builder->keyword("SELECT COUNT(*) FROM (")->raw($sql)->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    /**
     * @param string|array<string> $column
     */
    public function sum(string|array $column): float
    {
        $sql = $this->builder->getSQL();
        $this->builder = new SQLQueryBuilder();
        $this->builder->keyword("SELECT SUM(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->raw($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    /**
     * @param string|array<string> $column
     */
    public function avg(string|array $column): float
    {
        $sql = $this->builder->getSQL();
        $this->builder = new SQLQueryBuilder();
        $this->builder->keyword("SELECT AVG(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->raw($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }
    /**
     * @param string|array<string> $column
     */
    public function min(string|array $column): float
    {
        $sql = $this->builder->getSQL();
        $this->builder = new SQLQueryBuilder();
        $this->builder->keyword("SELECT MIN(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->raw($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    /**
     * @param string|array<string> $column
     */
    public function max(string|array $column): float
    {
        $sql = $this->builder->getSQL();
        $this->builder = new SQLQueryBuilder();
        $this->builder->keyword("SELECT MAX(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->raw($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(): array
    {
        $sql = $this->builder->getSQL();
        $results = $this->databaseAPI->query($sql);
        return $results;
    }

    public function fetchSingle(): mixed
    {
        $result = $this->fetchOne();
        if (is_null($result)) {
            return null;
        } else {
            return array_values($result)[0];
        }
    }

    public function fetchSingleOrFail(): mixed
    {
        $result = $this->fetchSingle();
        if (is_null($result)) {
            throw new Exception("Cannot fetch result. Query does not contain any records.");
        }
        return $result;
    }

    /**
     * @return null|array<string, mixed>
     */
    public function fetchOne(): array|null
    {
        $results = $this->fetchAll();
        return count($results) > 0 ? $results[0] : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchOneOrFail(): array
    {
        $result = $this->fetchOne();
        if (is_null($result)) {
            throw new Exception("Cannot fetch result. Query does not contain any records.");
        }
        return $result;
    }

    public function execute(): void
    {
        $sql = $this->builder->getSQL();
        $this->databaseAPI->execute($sql);
    }

    public function getInsertId(): int
    {
        return DB::get()->query("SELECT LAST_INSERT_ID()")->fetchSingleOrFail();
    }

    public function getAffectedRows(): int
    {
        return DB::get()->query("SELECT ROW_COUNT()")->fetchSingleOrFail();
    }

    public function startTransaction(): void
    {
        DB::get()->query("START TRANSACTION")->execute();
    }

    public function commit(): void
    {
        DB::get()->query("COMMIT")->execute();
    }

    public function rollback(): void
    {
        DB::get()->query("ROLLBACK")->execute();
    }

    /**
     * @param Closure(): void $callback
     */
    public function inTransaction(Closure $callback): void
    {
        try {
            $this->startTransaction();
            $callback();
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param array<mixed> $params
     */
    public function query(string $query, array $params = []): self
    {
        global $wpdb;
        $query = $wpdb->prepare($query, $params);
        $this->builder->raw($query);
        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): int
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $columnNames = array_keys($data);

        $this->builder->keyword("INSERT INTO")
            ->identifier($table)
            ->keyword("(")->identifierList($columnNames)->keyword(")")
            ->keyword("VALUES (")
            ->list($columnNames, ",", function ($builder, $columnName) use ($data) {
                $this->builder->value($data[$columnName]);
            })
            ->keyword(")");

        $this->execute();
        return $this->getInsertId();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $where
     */
    public function update(string $table, array $data, array $where = []): int
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $setColumnNames = array_keys($data);
        $whereColumnNames = array_keys($where);

        $this->builder->keyword("UPDATE")
            ->identifier($table)
            ->keyword("SET")
            ->list($setColumnNames, ",", function ($builder, $columnName) use ($data) {
                $builder->identifier($columnName)->keyword("=")->value($data[$columnName]);
            })
            ->keyword("WHERE")
            ->list($whereColumnNames, "AND", function ($builder, $columnName) use ($where) {
                $builder->identifier($columnName)->keyword("=")->value($where[$columnName]);
            });

        $this->execute();
        return $this->getAffectedRows();
    }

    /**
     * @param array<string, mixed> $where
     */
    public function delete(string $table, array $where = []): int
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $columnNames = array_keys($where);

        $this->builder->keyword("DELETE FROM")
            ->identifier($table)
            ->keyword("WHERE")
            ->list($columnNames, "AND", function ($builder, $columnName) use ($where) {
                $this->builder->identifier($columnName)->keyword("=")->value($where[$columnName]);
            });

        $this->execute();
        return $this->getAffectedRows();
    }
}
