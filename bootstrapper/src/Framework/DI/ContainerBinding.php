<?php

namespace __PLUGIN__\Framework\DI;

class ContainerBinding
{
    public function __construct(
        public string $identifier,
        public mixed $value,
        public ContainerBindingKind $bindingKind,
        public ContainerScopeKind $scopeKind
    ) {
    }
}
