<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Interfaces;

interface ColumnSchemaInterface
{
    public function type(string $type): self;
    public function nullable(): self;
    public function default(mixed $value): self;
    public function primary(): self;
    public function autoincrement(): self;
}
