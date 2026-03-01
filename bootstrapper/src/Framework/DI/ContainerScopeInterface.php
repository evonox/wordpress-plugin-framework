<?php

namespace __PLUGIN__\Framework\DI;

interface ContainerScopeInterface
{
    public function inSingleScoped(): void;
    public function isTransientScoped(): void;
}
