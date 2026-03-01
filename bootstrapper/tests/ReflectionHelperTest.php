<?php

namespace Tests;

use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\Attributes\WPAction;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ClassA;
use __PLUGIN__\Framework\Helpers\ReflectionHelper;
use __PLUGIN__\Framework\Attributes\PluginPrefix;

class ReflectionHelperTest extends TestCase
{
    public function testClassAttribute(): void
    {
        $attr = ReflectionHelper::getClassAttribute(ClassA::class, PluginPrefix::class);
        $this->assertTrue($attr !== false);
        $this->assertTrue($attr instanceof PluginPrefix);
    }

    public function testPropertyAttribute(): void
    {
        $properties = ReflectionHelper::getInstanceProperties(ClassA::class);
        $attr = ReflectionHelper::getPropertyAttribute($properties[0], Inject::class);
        $this->assertTrue($attr !== false);
        $this->assertTrue($attr instanceof Inject);
    }

    public function testMethodAttribute(): void
    {
        $methods = ReflectionHelper::getInstanceMethods(ClassA::class);
        $attr = ReflectionHelper::getMethodAttribute($methods[0], WPAction::class);
        $this->assertTrue($attr !== false);
        $this->assertTrue($attr instanceof WPAction);
    }

    public function testParameterAttribute(): void
    {
        $parameters = ReflectionHelper::getConstructorParameters(ClassA::class);
        $attr = ReflectionHelper::getParameterAttribute($parameters[0], Inject::class);
        $this->assertTrue($attr !== false);
        $this->assertTrue($attr instanceof Inject);
    }

    public function testGetMethod(): void
    {
        $instance = new ClassA("1.0");
        $method = ReflectionHelper::getMethod($instance, "method");
        $this->assertTrue($method !== false);
        $this->assertTrue($method->getName() === "method");
    }

    public function testGetParamType(): void
    {
        $instance = new ClassA("1.0");
        $method = ReflectionHelper::getMethod($instance, "method");
        $this->assertTrue($method !== false);
        $paramType = ReflectionHelper::getParameterType($method, 0);
        $this->assertTrue($paramType === ClassA::class);
    }
}
