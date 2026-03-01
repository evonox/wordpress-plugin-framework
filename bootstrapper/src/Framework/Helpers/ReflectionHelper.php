<?php

namespace __PLUGIN__\Framework\Helpers;

use stdClass;
use ReflectionClass;
use ReflectionMethod;
use Exception;
use Attribute;
use ReflectionParameter;
use ReflectionProperty;

class ReflectionHelper
{
    public static function getParameterType(ReflectionMethod $method, int $paramIndex): string
    {
        $parameters = $method->getParameters();
        if (isset($parameters[$paramIndex]) === false) {
            throw new Exception("Invalid parameter index: $paramIndex");
        }
        $parameter = $parameters[$paramIndex];
        $type = $parameter->getType();
        if (is_null($type)) {
            throw new Exception("Missing type annotation: $paramIndex");
        }
        return (string)$type;
    }

    public static function getMethod(object $instance, string $methodName): ReflectionMethod|false
    {
        $reflection = new ReflectionClass($instance);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getName() === $methodName) {
                return $method;
            }
        }

        return false;
    }

    /**
     * @return array<ReflectionParameter>
     */
    public static function getConstructorParameters(string $className): array
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [];
        } else {
            return $constructor->getParameters();
        }
    }

    /**
     * @return array<ReflectionProperty>
     */
    public static function getInstanceProperties(string $className): array
    {
        $reflection = new \ReflectionClass($className);

        $methods = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        return array_filter($methods, function ($method) {
            return $method->isStatic() === false;
        });
    }

    /**
     * @return array<ReflectionMethod>
     */
    public static function getInstanceMethods(string $className): array
    {
        $reflection = new ReflectionClass($className);

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        return array_filter($methods, function ($method) {
            return $method->isStatic() === false;
        });
    }

    public static function getClassAttribute(string $className, string $attributeName): object|false
    {
        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->getName() === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance;
            }
        }

        return false;
    }

    public static function getPropertyAttribute(ReflectionProperty $property, string $attributeName): object|false
    {
        $attributes = $property->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->getName() === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance;
            }
        }

        return false;
    }

    public static function getMethodAttribute(ReflectionMethod $method, string $attributeName): object|false
    {
        $attributes = $method->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->getName() === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance;
            }
        }

        return false;
    }

    public static function getParameterAttribute(ReflectionParameter $parameter, string $attributeName): object|false
    {
        $attributes = $parameter->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->getName() === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance;
            }
        }

        return false;
    }
}
