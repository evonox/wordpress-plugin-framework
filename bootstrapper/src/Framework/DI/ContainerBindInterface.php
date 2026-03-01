<?php

namespace __PLUGIN__\Framework\DI;

interface ContainerBindInterface
{
    public function toSelf(): ContainerScopeInterface;
    public function toClass(string $className): ContainerScopeInterface;
    public function toLazyClass(string $className): ContainerScopeInterface;
    public function toConstantValue(mixed $token): void;
    public function toDynamicValue(callable $lambda): void;
    public function toLazyFactory(callable $lambda): ContainerScopeInterface;
}
