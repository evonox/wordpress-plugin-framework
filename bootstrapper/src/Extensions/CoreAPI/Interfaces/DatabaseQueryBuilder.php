<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

use Closure;

interface DatabaseQueryBuilder
{
    public function getSQL(): string;
    /**
     * @param string|array<string> $columns
     */
    public function select(string|array ...$columns): self;
    public function from(string $table, ?string $alias = null): self;
    public function join(string $table, ?string $alias = null): self;
    public function leftJoin(string $table, ?string $alias = null): self;
    public function rightJoin(string $table, ?string $alias = null): self;
    /**
     * @param string|array<string> $column1
     * @param string|array<string> $column2
     */
    public function on(string|array $column1, string|array $column2): self;
    /**
     * @param string|array<string> $column
     */
    public function where(string|array $column, string $operator, mixed $value): self;
    /**
     * @param string|array<string> $column
     */
    public function whereNull(string|array $column): self;
    /**
     * @param string|array<string> $column
     */
    public function whereNotNull(string|array $column): self;
    /**
     * @param string|array<string> $column
     * @param array<mixed> $values
     */
    public function whereIn(string|array $column, array $values): self;
    /**
     * @param string|array<string> $column
     * @param array<mixed> $values
     */
    public function whereNotIn(string|array $column, array $values): self;
    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function whereExists(Closure $callback): self;
    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function whereNotExists(Closure $callback): self;
    public function not(): self;
    public function or(): self;
    public function and(): self;
    /**
     * @param Closure(DatabaseQueryBuilder): void $callback
     */
    public function inParen(Closure $callback): self;
    /**
     * @param string|array<string> $columns
     */
    public function groupBy(string|array ...$columns): self;
    /**
     * @param string|array<string> $column
     */
    public function orderBy(string|array $column, string $order = "ASC"): self;
    /**
     * @param string|array<string> $column
     */
    public function having(string|array $column, string $operator, mixed $value): self;
    /**
     * @param string|array<string> $column
     */
    public function havingNull(string|array $column): self;
    /**
     * @param string|array<string> $column
     */
    public function havingNotNull(string|array $column): self;

    public function limit(int $limit, int $offset = 0): self;

    public function count(): int;
    /**
     * @param string|array<string> $column
     */
    public function sum(string|array $column): float;
    /**
     * @param string|array<string> $column
     */
    public function avg(string|array $column): float;
    /**
     * @param string|array<string> $column
     */
    public function min(string|array $column): float;
    /**
     * @param string|array<string> $column
     */
    public function max(string|array $column): float;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(): array;
    public function fetchSingle(): mixed;
    public function fetchSingleOrFail(): mixed;
    /**
     * @return null|array<string, mixed>
     */
    public function fetchOne(): array|null;
    /**
     * @return array<string, mixed>
     */
    public function fetchOneOrFail(): array;
    public function execute(): void;
    public function getInsertId(): int;
    public function getAffectedRows(): int;

    public function startTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
    /**
     * @param Closure(): void $callback
     */
    public function inTransaction(Closure $callback): void;

    /**
     * @param array<mixed> $params
     */
    public function query(string $query, array $params = []): self;
    /**
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): int;
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $where
     */
    public function update(string $table, array $data, array $where = []): int;
    /**
     * @param array<string, mixed> $where
     */
    public function delete(string $table, array $where = []): int;
}
