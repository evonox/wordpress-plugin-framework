<?php

namespace __PLUGIN__\Framework\DI;

use Closure;

class LazyProxy
{
    private ?object $instance = null;

    public function __construct(private Closure $factory)
    {
        $this->factory = $factory;
    }

    private function getInstance(): object
    {
        if ($this->instance === null) {
            $this->instance = ($this->factory)();
        }
        return $this->instance;
    }

    /**
     * @param array<mixed> $args
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->getInstance()->$method(...$args);
    }
}
