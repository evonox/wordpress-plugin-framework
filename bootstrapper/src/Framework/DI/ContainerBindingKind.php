<?php

namespace __PLUGIN__\Framework\DI;

enum ContainerBindingKind
{
    case Clazz;
    case LazyClazz;
    case ConstantValue;
    case DynamicValue;
    case LazyFactory;
}
