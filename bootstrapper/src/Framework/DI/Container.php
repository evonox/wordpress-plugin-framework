<?php

namespace __PLUGIN__\Framework\DI;

use stdClass;

class Container implements ContainerInterface
{
    private ContainerBindingRegistry $registry;
    /** @var array<mixed> */
    private array $cache = [];

    private static Container|null $instance = null;

    public static function get(): ContainerInterface
    {
        if (self::$instance === null) {
            self::$instance = new Container();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->registry = new ContainerBindingRegistry();
    }

    public function isBound(string $identifier): bool
    {
        return $this->registry->isIdentifierBound($identifier);
    }

    public function bind(string $identifier): ContainerBindInterface
    {
        return new BindingFluentBuilder($this->registry, $identifier);
    }

    public function unbind(string $identifier): void
    {
        $this->registry->unbindIdentifier($identifier);
    }

    public function rebind(string $identifier): ContainerBindInterface
    {
        $this->unbind($identifier);
        return $this->bind($identifier);
    }

    public function resolve(string $identifier): mixed
    {
        $scope = $this->registry->getScopeForIdentifier($identifier);
        if ($scope === ContainerScopeKind::Transient) {
            return $this->resolveValue($identifier, false);
        } else {
            if (isset($this->cache[$identifier])) {
                return $this->cache[$identifier];
            } else {
                return $this->resolveValue($identifier, true);
            }
        }
    }

    public function make(string $className): stdClass
    {
        $constructorTypes = DIHelper::getConstructorInjectionTypes($className);
        $constructorValues = array_map(function (string $identifier) {
            return $this->resolve($identifier);
        }, $constructorTypes);

        $instance = new $className($constructorValues);

        $propertyInjectionTypes = DIHelper::getPropertyInjectionTypes($instance);
        foreach ($propertyInjectionTypes as $propertyName => $identifier) {
            $instance->{$propertyName} = $this->resolve($identifier);
        }

        $postConstructFn = DIHelper::getPostConstructMethod($instance);
        if (is_string($postConstructFn)) {
            $instance->{$postConstructFn}();
        }

        return $instance;
    }

    private function resolveValue(string $identifier, bool $addToCache): mixed
    {
        $bindingKind = $this->registry->getBindingKindForIdentifier($identifier);
        $value = $this->registry->getValueForIdentifier($identifier);
        $valueInstance = null;

        switch ($bindingKind) {
            case ContainerBindingKind::Clazz:
                $valueInstance = $this->make($value);
                break;
            case ContainerBindingKind::ConstantValue:
                $valueInstance = $value;
                break;
            case ContainerBindingKind::DynamicValue:
                $valueInstance = $value();
                break;
            case ContainerBindingKind::LazyClazz:
                $valueInstance = new LazyProxy(function () use ($value) {
                    return $this->make($value);
                });
                break;
            case ContainerBindingKind::LazyFactory:
                $valueInstance = new LazyProxy(function () use ($value, $identifier) {
                    return $value($identifier);
                });
                break;
        }

        if ($addToCache) {
            $this->cache[$identifier] = $valueInstance;
        }

        return $valueInstance;
    }
}
