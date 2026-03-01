<?php

namespace __PLUGIN__\Framework\DI;

enum ContainerScopeKind
{
    case Singleton;
    case Transient;
}
