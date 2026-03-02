<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Common;

class SchemaQuery
{
    /**
     * @param array<string> $columnNames
     */
    public static function getForeignKeyName(string $tableName, array $columnNames): string
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = %s
            AND COLUMN_NAME = %s ;
        ", $tableName, $columnNames[0]);

        $constraintName = $wpdb->get_var($sql);
        return $constraintName;
    }
}
