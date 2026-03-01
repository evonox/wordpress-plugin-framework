<?php

namespace __PLUGIN__\Framework\DI;

use Exception;

class BindingFluentBuilder implements ContainerBindInterface, ContainerScopeInterface
{
    private mixed $value;
    private ContainerBindingKind $bindingKind;
    private ContainerScopeKind $scopeKind;

    public function __construct(private ContainerBindingRegistry $registry, private string $identifier)
    {
    }

    public function toSelf(): ContainerScopeInterface
    {
        return $this->toClass($this->identifier);
    }

    public function toClass(string $className): ContainerScopeInterface
    {
        if (class_exists($className) === false) {
            throw new Exception("Cannot bind identifier '{$this->identifier}'. Class '$className' does not exist.");
        }
        $this->value = $className;
        $this->bindingKind = ContainerBindingKind::Clazz;
        return $this;
    }

    public function toLazyClass(string $className): ContainerScopeInterface
    {
        if (class_exists($className) === false) {
            throw new Exception("Cannot bind identifier '{$this->identifier}'. Class '$className' does not exist.");
        }
        $this->value = $className;
        $this->bindingKind = ContainerBindingKind::LazyClazz;
        return $this;
    }

    public function toConstantValue(mixed $token): void
    {
        $this->value = $token;
        $this->bindingKind = ContainerBindingKind::ConstantValue;
        $this->scopeKind = ContainerScopeKind::Singleton;
        $this->insertBindingToRegistry();
    }

    public function toDynamicValue(callable $lambda): void
    {
        $this->value = $lambda;
        $this->bindingKind = ContainerBindingKind::DynamicValue;
        $this->scopeKind = ContainerScopeKind::Singleton;
        $this->insertBindingToRegistry();
    }

    public function toLazyFactory(callable $lambda): ContainerScopeInterface
    {
        $this->value = $lambda;
        $this->bindingKind = ContainerBindingKind::LazyFactory;
        return $this;
    }

    public function isSingleScoped(): void
    {
        $this->scopeKind = ContainerScopeKind::Singleton;
        $this->insertBindingToRegistry();
    }

    public function isTransientScoped(): void
    {
        $this->scopeKind = ContainerScopeKind::Transient;
        $this->insertBindingToRegistry();
    }

    private function insertBindingToRegistry(): void
    {
        $this->registry->bindIdentifier($this->identifier, $this->value, $this->bindingKind, $this->scopeKind);
    }
}
