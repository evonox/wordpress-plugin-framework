<?php

namespace __PLUGIN__\Framework\DI;

interface ContainerInterface
{
    public function clear(): void;
    public function isBound(string $identifier): bool;
    public function bind(string $identifier): ContainerBindInterface;
    public function unbind(string $identifier): void;
    public function rebind(string $identifier): ContainerBindInterface;

    public function resolve(string $className): mixed;
    public function make(string $className): mixed;
}
