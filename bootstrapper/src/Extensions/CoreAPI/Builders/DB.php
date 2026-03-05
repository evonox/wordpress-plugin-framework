<?php

namespace __PLUGIN__\Extensions\CoreAPI\Builders;

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

    /**
     * @param string|array<string> $columns
     */
    public function select(string|array ...$columns): self
    {
        $this->builder->keyword("SELECT");
        foreach ($columns as $column) {
            if (is_string($column)) {
                $this->builder->identifier($column);
            } elseif (is_array($column)) {
                $this->builder->identifier($column[0])->keyword("AS")->identifier($column[1]);
            }
        }
        return $this;
    }

    public function from(string $table, ?string $alias = null): self
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $this->builder->keyword("FROM")->identifier($table);
        if ($alias !== null) {
            $this->builder->keyword("AS")->identifier($alias);
        }
        return $this;
    }

    public function join(string $table, ?string $alias = null): self
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $this->builder->keyword("JOIN")->identifier($table);
        if ($alias !== null) {
            $this->builder->keyword("AS")->identifier($alias);
        }
        return $this;
    }

    public function leftJoin(string $table, ?string $alias = null): self
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $this->builder->keyword("LEFT JOIN")->identifier($table);
        if ($alias !== null) {
            $this->builder->keyword("AS")->identifier($alias);
        }
        return $this;
    }

    public function rightJoin(string $table, ?string $alias = null): self
    {
        $table = $this->databaseAPI->escapeTableName($table);
        $this->builder->keyword("RIGHT JOIN")->identifier($table);
        if ($alias !== null) {
            $this->builder->keyword("AS")->identifier($alias);
        }
        return $this;
    }

    public function on(string $column1, string $column2): self
    {
        $this->builder->keyword("ON")->identifier($column1)->keyword("=")->identifier($column2);
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->identifier($column)->keyword($operator)->value($value);
        return $this;
    }

    public function whereNull(string $column): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->identifier($column)->keyword("IS NULL");
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->identifier($column)->keyword("IS NOT NULL");
        return $this;
    }

    /**
     * @param array<mixed> $values
     */
    public function whereIn(string $column, array $values): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->identifier($column)
            ->keyword("IN (")
            ->list($values, ",", function ($builder, $value) {
                $builder->value($value);
            })
            ->keyword(")");

        return $this;
    }

    /**
     * @param array<mixed> $values
     */
    public function whereNotIn(string $column, array $values): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->identifier($column)
            ->keyword("NOT IN (")
            ->list($values, ",", function ($builder, $value) {
                $builder->value($value);
            })
            ->keyword(")");

        return $this;
    }

    /**
     * @param Closure(SQLQueryBuilder): void $callback
     */
    public function whereExists(Closure $callback): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->keyword("EXISTS");
        $callback($this->builder);
        return $this;
    }

    /**
     * @param Closure(SQLQueryBuilder): void $callback
     */
    public function whereNotExists(Closure $callback): self
    {
        if (! $this->whereKWAdded) {
            $this->builder->keyword("WHERE");
            $this->whereKWAdded = true;
        }
        $this->builder->keyword("NOT EXISTS");
        $callback($this->builder);
        return $this;
    }

    public function not(): self
    {
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
     * @param Closure(SQLQueryBuilder): void $callback
     */
    public function inParen(Closure $callback): self
    {
        $this->builder->keyword("(");
        $callback($this->builder);
        $this->builder->keyword(")");
        return $this;
    }

    /**
     * @param string|array<string> $column
     */
    public function groupBy(string|array $column): self
    {
        if (is_string($column)) {
            $column = [$column];
        }
        $this->builder->keyword("GROUP BY")->identifierList($column);
        return $this;
    }

    public function orderBy(string $column, string $order = "ASC"): self
    {
        if (!$this->orderByKWAdded) {
            $this->builder->keyword("ORDER BY");
            $this->orderByKWAdded = true;
        } else {
            $this->builder->keyword(",");
        }
        $this->builder->identifier($column)->keyword($order);
        return $this;
    }

    public function having(string $column, string $operator, mixed $value): self
    {
        if (!$this->havingKWAdded) {
            $this->builder->keyword("HAVING");
            $this->havingKWAdded = true;
        }
        $this->builder->identifier($column)->keyword($operator)->value($value);
        return $this;
    }

    public function havingNull(string $column): self
    {
        if (!$this->havingKWAdded) {
            $this->builder->keyword("HAVING");
            $this->havingKWAdded = true;
        }
        $this->builder->identifier($column)->keyword("IS NULL");
        return $this;
    }

    public function havingNotNull(string $column): self
    {
        if (!$this->havingKWAdded) {
            $this->builder->keyword("HAVING");
            $this->havingKWAdded = true;
        }
        $this->builder->identifier($column)->keyword("IS NOT NULL");
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->builder->keyword("LIMIT")->value($limit)->keyword("OFFSET")->value($offset);
        return $this;
    }

    public function count(): int
    {
        $sql = $this->builder->getSQL();
        $builder = new SQLQueryBuilder();
        $builder->keyword("SELECT COUNT(*) FROM (")->keyword($sql)->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    public function sum(string $column): int
    {
        $sql = $this->builder->getSQL();
        $builder = new SQLQueryBuilder();
        $builder->keyword("SELECT SUM(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->keyword($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    public function avg(string $column): int
    {
        $sql = $this->builder->getSQL();
        $builder = new SQLQueryBuilder();
        $builder->keyword("SELECT AVG(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->keyword($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }
    public function min(string $column): int
    {
        $sql = $this->builder->getSQL();
        $builder = new SQLQueryBuilder();
        $builder->keyword("SELECT MIN(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->keyword($sql)
            ->keyword(") AS q");
        return $this->fetchSingleOrFail();
    }

    public function max(string $column): int
    {
        $sql = $this->builder->getSQL();
        $builder = new SQLQueryBuilder();
        $builder->keyword("SELECT MAX(q.")
            ->identifier($column)
            ->keyword(") FROM (")
            ->keyword($sql)
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
        $this->builder = new SQLQueryBuilder();
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
        $this->builder = new SQLQueryBuilder();
    }

    public function getInsertId(): int
    {
        return $this->query("SELECT LAST_INSERT_ID()")->fetchSingleOrFail();
    }

    public function getAffectedRows(): int
    {
        return $this->query("SELECT ROW_COUNT()")->fetchSingleOrFail();
    }

    public function startTransaction(): void
    {
        $this->query("START TRANSACTION")->execute();
    }

    public function commit(): void
    {
        $this->query("COMMIT")->execute();
    }

    public function rollback(): void
    {
        $this->query("ROLLBACK")->execute();
    }

    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function inTransaction(Closure $callback): void
    {
        try {
            $this->startTransaction();
            $callback($this);
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param array<mixed> $params
     */
    // TODO: ESCAPING PARAMETERS
    public function query(string $query, array $params = []): self
    {
        $this->builder->keyword($query);
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
