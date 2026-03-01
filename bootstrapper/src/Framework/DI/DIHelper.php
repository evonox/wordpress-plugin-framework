<?php

namespace __PLUGIN__\Framework\DI;

use __PLUGIN__\Framework\Helpers\ReflectionHelper;
use __PLUGIN__\Framework\Attributes\PostConstruct;
use __PLUGIN__\Framework\Attributes\Inject;
use Exception;

class DIHelper
{
    /**
     * @return array<string>
     */
    public static function getConstructorInjectionTypes(string $className): array
    {
        $parameterMap = [];
        $parameters = ReflectionHelper::getConstructorParameters($className);
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $attribute = ReflectionHelper::getParameterAttribute($parameter, Inject::class);
            if ($attribute === false) {
                $type = $parameter->getType();
                if (is_null($type)) {
                    throw new Exception(
                        "Constructor parameter '$parameterName' has missing type annotation in class '$className'."
                    );
                }
                $parameterMap[$parameterName] = strval($type);
            } else {
                $parameterMap[$parameterName] = $attribute->identifier;
            }
        }

        return $parameterMap;
    }

    /**
     * @return array<string>
     */
    public static function getPropertyInjectionTypes(object $instance): array
    {
        $propertyMap = [];
        $properties = ReflectionHelper::getInstanceProperties(get_class($instance));
        foreach ($properties as $property) {
            $attribute = ReflectionHelper::getPropertyAttribute($property, Inject::class);
            if ($attribute !== false) {
                $identifier = $attribute->identifier;
                if (is_null($identifier)) {
                    $type = $property->getType();
                    if (is_null($type)) {
                        throw new Exception(
                            "Property '{$property->getName()}' has missing type annotation in class '"
                            . get_class($instance) . "'."
                        );
                    }
                    $propertyMap[$property->getName()] = strval($type);
                } else {
                    $propertyMap[$property->getName()] = $attribute->identifier;
                }
            }
        }
        return $propertyMap;
    }

    public static function getPostConstructMethod(object $instance): string|null
    {
        $methods = ReflectionHelper::getInstanceMethods(get_class($instance));
        foreach ($methods as $method) {
            $attribute = ReflectionHelper::getMethodAttribute($method, PostConstruct::class);
            if ($attribute !== false) {
                return $method->getName();
            }
        }
        return null;
    }
}
