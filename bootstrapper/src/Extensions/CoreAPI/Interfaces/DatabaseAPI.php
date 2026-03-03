<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

interface DatabaseAPI
{
    /**
     * @param array<mixed> $args
     */
    public function execute(string $SQL, array $args = []): void;

    /**
     * @param array<mixed> $args
     * @return array<int, array<string, mixed>>
     */
    public function query(string $SQL, array $args = []): array;

    /**
     * @param array<string, mixed> $data
     * @param array<string, string>|null $format
     */
    public function insert(string $table, array $data, ?array $format = null): int;

    /**
     * @param array<string, mixed> $where
     * @param array<string, mixed> $data
     * @param array<string, string>|null $format
     */
    public function update(string $table, array $data, array $where, ?array $format = null): void;

    /**
     * @param array<string, mixed> $data
     * @param array<string, string>|null $format
     */
    public function upsert(string $table, array $data, ?array $format = null): void;
    /**
     * @param array<string, mixed> $where
     * @param array<string, string>|null $format
     */
    public function delete(string $table, array $where, ?array $format = null): void;
    public function escapeTableName(string $table): string;
    public function escapeIdentifier(string $identifier): string;
}
