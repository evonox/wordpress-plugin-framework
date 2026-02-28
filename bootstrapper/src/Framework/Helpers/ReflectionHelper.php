<?php

namespace __PLUGIN__\Framework\Helpers;

use stdClass;
use ReflectionClass;
use ReflectionMethod;
use Exception;
use Attribute;

class ReflectionHelper
{
    public static function getParamType(ReflectionMethod $method, int $paramIndex): string
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

    public static function getMethod(stdClass $instance, string $methodName): ReflectionMethod
    {
        $reflection = new ReflectionClass($instance);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getName() === $methodName) {
                return $method;
            }
        }

        throw new \Exception("ReflectionError: $methodName is not defined on the instance.");
    }

    /**
     * @return array<ReflectionMethod>
     */
    public static function getInstanceMethods(string $className): array
    {
        $reflection = new \ReflectionClass($className);

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        return array_filter($methods, function ($method) {
            return $method->isStatic() === false;
        });
    }

    public static function getClassAttribute(string $className, string $attributeName): Attribute|false
    {
        $reflection = new \ReflectionClass($className);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attr) {
            if ($attr->getName() === $attributeName) {
                $attrInstance = $attr->newInstance();
                return $attrInstance;
            }
        }

        return false;
    }

    public static function getMethodAttribute(ReflectionMethod $method, string $attributeName): Attribute|false
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
}
