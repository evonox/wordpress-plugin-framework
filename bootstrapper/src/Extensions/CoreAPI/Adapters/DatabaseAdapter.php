<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\DatabaseAPI;
use __PLUGIN__\Framework\Attributes\Inject;

class DatabaseAdapter implements DatabaseAPI
{
    #[Inject("PluginPrefix")]
    private string $pluginPrefix = "";

    private const string NO_PREFIXING_TOKEN = "::";
    /**
     * @inheritDoc
     */
    public function delete(string $table, array $where, ?array $format = null): void
    {
        global $wpdb;
        $table = $this->escapeTableName($table);
        $format = $this->detectArgumentFormats($where, $format);
        $wpdb->delete($table, $where, $format);
    }

    /**
     * @inheritDoc
     */
    public function escapeIdentifier(string $identifier): string
    {
        global $wpdb;
        return $wpdb->escape_identifiers($identifier);
    }

    /**
     * @inheritDoc
     */
    public function escapeTableName(string $table): string
    {
        global $wpdb;
        if (str_starts_with($table, self::NO_PREFIXING_TOKEN)) {
            $table = substr($table, strlen(self::NO_PREFIXING_TOKEN));
        } else {
                $table = $this->pluginPrefix . "_" . $table;
        }
        $table = $wpdb->prefix . $table;
        return $this->escapeIdentifier($table);
    }

    /**
     * @inheritDoc
     */
    public function execute(string $SQL, array $args = []): void
    {
        global $wpdb;
        $SQL = $wpdb->prepare($SQL, $args);
        $wpdb->query($SQL);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $data, ?array $format = null): int
    {
        global $wpdb;
        $table = $this->escapeTableName($table);
        $format = $this->detectArgumentFormats($data, $format);
        $wpdb->insert($table, $data, $format);
        return (int)$wpdb->insert_id;
    }

    /**
     * @inheritDoc
     */
    public function query(string $SQL, array $args = []): array
    {
        global $wpdb;
        $SQL = $wpdb->prepare($SQL, $args);
        return $wpdb->get_results($SQL, ARRAY_A);
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $data, array $where, ?array $format = null): void
    {
        global $wpdb;
        $table = $this->escapeTableName($table);
        $dataFormat = $this->detectArgumentFormats($data, $format);
        $whereFormat = $this->detectArgumentFormats($where, $format);
        $wpdb->update($table, $data, $where, $dataFormat, $whereFormat);
    }

    /**
     * @inheritDoc
     */
    public function upsert(string $table, array $data, ?array $format = null): void
    {
        global $wpdb;
        $table = $this->escapeTableName($table);
        $format = $this->detectArgumentFormats($data, $format);
        $wpdb->replace($table, $data, $format);
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string>|null $format
     * @return array<int, string>
     */
    private function detectArgumentFormats(array $data, ?array $format = null): array
    {
        $format = $format ?? [];
        $result = [];
        foreach ($data as $columnName => $value) {
            if (isset($format[$columnName])) {
                $result[] = $format[$columnName];
            } else {
                $result[] = $this->detectValueFormat($value);
            }
        }
        return $result;
    }

    private function detectValueFormat(mixed $value): string
    {
        if (is_int($value) || is_bool($value)) {
            return '%d';
        }
        if (is_float($value)) {
            return '%f';
        }
        return '%s';
    }
}
