<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Common;

use Closure;

/**
 * Class to build and synthetize SQL Query for running migrations
 */
class SQLQueryBuilder
{
    private string $sqlQuery = "";

    public function getSQL(): string
    {
        return $this->sqlQuery;
    }

    /**
     * @param array<string> $values
     */
    public function identifierList(array $values): self
    {
        $this->list($values, ",", function ($builder, $value) {
            $builder->identifier($value);
        });
        return $this;
    }

    /**
     * @param array<mixed> $values
     */
    public function list(array $values, string $separator, Closure $itemClosure): self
    {
        for ($i = 0; $i < count($values); $i++) {
            if ($i > 0) {
                $this->appendContent($separator);
            }
            $value = $values[$i];
            call_user_func($itemClosure, $this, $value);
        }

        return $this;
    }

    public function keyword(string $value): self
    {
        $this->appendContent($value);
        return $this;
    }

    public function identifier(string $value): self
    {
        $this->appendContent("`$value`");
        return $this;
    }

    public function value(mixed $value): self
    {
        if (is_string($value)) {
            $this->string($value);
        } elseif (is_numeric($value)) {
            $this->number($value);
        } elseif (is_bool($value)) {
            $this->boolean($value);
        } else {
            throw new \Exception("As a value you can only pass strings, numbers and booleans to SQLQueryBuilder.");
        }
        return $this;
    }

    public function finalize(): void
    {
        $this->appendContent(";");
    }

    private function string(string $value): void
    {
        $value = esc_sql($value);
        $this->appendContent("'$value'");
    }

    private function number(int $value): void
    {
        $this->appendContent(strval($value));
    }

    private function boolean(bool $value): void
    {
        if ($value === true) {
            $this->number(1);
        } else {
            $this->number(0);
        }
    }

    private function appendContent(string $value): void
    {
        $this->sqlQuery = $this->sqlQuery . $value . " ";
    }
}
