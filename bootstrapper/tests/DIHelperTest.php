<?php

namespace Tests;

use __PLUGIN__\Framework\DI\DIHelper;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ClassA;
use Tests\Helper\ClassB;

class DIHelperTest extends TestCase
{
    public function testIfConstructorInjectionTypesAreExtracted(): void
    {
        $result = DIHelper::getConstructorInjectionTypes(ClassB::class);
        $this->assertEquals(
            [
                "pluginVersion" => "PluginVersion",
                "classA" => ClassA::class,
            ],
            $result
        );
    }

    public function testIfPropertyInjectionTypesAreExtracted(): void
    {
        $instance = new ClassB("1.0.0", new ClassA("1.0.0"));
        $result = DIHelper::getPropertyInjectionTypes($instance);
        $this->assertEquals(
            [
                "pluginVersion" => "PluginVersion",
                "classA" => ClassA::class,
            ],
            $result
        );
    }

    public function testIfPostConstructMethodsAreExtracted(): void
    {
        $instance = new ClassB("1.0.0", new ClassA("1.0.0"));
        $result = DIHelper::getPostConstructMethod($instance);
        $this->assertEquals(
            "handlePostConstruct",
            $result
        );
    }
}
