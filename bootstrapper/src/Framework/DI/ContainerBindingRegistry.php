<?php

namespace __PLUGIN__\Framework\DI;

use Exception;

class ContainerBindingRegistry
{
    /** @var array<ContainerBinding> */
    private array $bindings = [];

    public function isIdentifierBound(string $identifier): bool
    {
        return $this->lookupValueForIdentifier($identifier) !== false;
    }

    public function bindIdentifier(
        string $identifier,
        mixed $value,
        ContainerBindingKind $bindingKind,
        ContainerScopeKind $scopeKind
    ): void {
        if ($this->isIdentifierBound($identifier)) {
            throw new Exception("Identifier '$identifier' is already bound");
        }
        $this->bindings[] = new ContainerBinding($identifier, $value, $bindingKind, $scopeKind);
    }

    public function unbindIdentifier(string $identifier): void
    {
        $binding = $this->lookupValueForIdentifier($identifier);
        if ($binding === false) {
            throw new Exception("Identifier '$identifier' is not bounded.");
        }
        $key = array_search($binding, $this->bindings);
        unset($this->bindings[$key]);
    }

    public function getValueForIdentifier(string $identifier): mixed
    {
        $binding = $this->lookupValueForIdentifier($identifier);
        return $binding === false ? false : $binding->value;
    }

    public function getBindingKindForIdentifier(string $identifier): ContainerBindingKind|false
    {
        $binding = $this->lookupValueForIdentifier($identifier);
        return $binding === false ? false : $binding->bindingKind;
    }

    public function getScopeForIdentifier(string $identifier): ContainerScopeKind|false
    {
        $binding = $this->lookupValueForIdentifier($identifier);
        return $binding === false ? false : $binding->scopeKind;
    }

    private function lookupValueForIdentifier(string $identifier): ContainerBinding|false
    {
        foreach ($this->bindings as $binding) {
            if ($binding->identifier === $identifier) {
                return $binding;
            }
        }
        return false;
    }
}
