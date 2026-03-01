<?php

namespace __PLUGIN__\Framework\DI;

interface ContainerScopeInterface
{
    public function isSingleScoped(): void;
    public function isTransientScoped(): void;
}
