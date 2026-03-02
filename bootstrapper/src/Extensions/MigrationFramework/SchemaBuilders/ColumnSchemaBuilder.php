<?php

namespace __PLUGIN__\Extensions\MigrationFramework\SchemaBuilders;

use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;
use __PLUGIN__\Extensions\MigrationFramework\Interfaces\ColumnSchemaInterface;

class ColumnSchemaBuilder implements ColumnSchemaInterface
{
    private string $name;
    private string $type;
    private bool $nullable = false;
    private mixed $default = null;
    private bool $isPrimary = false;
    private bool $isAutoIncrement = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getColumnName(): string
    {
        return $this->name;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    public function default(mixed $value): self
    {
        $this->default = $value;
        return $this;
    }

    public function primary(): self
    {
        $this->isPrimary = true;
        return $this;
    }

    public function isPrimaryKey(): bool
    {
        return $this->isPrimary;
    }

    public function autoincrement(): self
    {
        $this->isAutoIncrement = true;
        return $this;
    }

    public function buildSQL(SQLQueryBuilder $builder, bool $appendPrimary = false): void
    {
        $builder->identifier($this->name)->keyword($this->type);

        if ($this->nullable) {
            $builder->keyword("NULL");
        } else {
            $builder->keyword("NOT NULL");
        }

        if (is_null($this->default) === false) {
            $builder->keyword("DEFAULT (")->value($this->default)->keyword(")");
        }

        if ($this->isAutoIncrement === true) {
            $builder->keyword("AUTO_INCREMENT");
        }

        if ($this->isPrimary && $appendPrimary) {
            $builder->keyword("PRIMARY KEY");
        }
    }
}
