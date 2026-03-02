<?php

namespace Tests;

use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ClassC;

class ContainerHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Container::get()->clear();
    }

    public function testIfContainerBindsAndResolves(): void
    {
        $container = Container::get();
        $container->bind('test_constant')->toConstantValue(42);
        $this->assertTrue($container->isBound('test_constant'));
        $this->assertEquals(42, $container->resolve('test_constant'));

        $container->unbind('test_constant');
        $this->assertFalse($container->isBound('test_constant'));
    }

    public function testIfContainerRebindsAndResolves(): void
    {
        $container = Container::get();
        $container->bind('test_rebind')->toConstantValue(42);
        $this->assertEquals(42, $container->resolve('test_rebind'));

        $container->rebind('test_rebind')->toConstantValue(100);
        $this->assertEquals(100, $container->resolve('test_rebind'));
    }

    public function testIfContainerResolvesConstantValueAsSingleton(): void
    {
        $container = Container::get();
        $container->bind('test_singleton')->toConstantValue(new \stdClass());
        $instance1 = $container->resolve('test_singleton');
        $instance2 = $container->resolve('test_singleton');
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContinaerResolvesDynamicValueAsSingleton(): void
    {
        $container = Container::get();
        $container->bind('test_dynamic_singleton')->toDynamicValue(function() {
            return new \stdClass();
        });
        $instance1 = $container->resolve('test_dynamic_singleton');
        $instance2 = $container->resolve('test_dynamic_singleton');
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesSelfBoundClassAsSingleton(): void
    {
        $container = Container::get();
        $container->bind(ClassC::class)->toSelf()->isSingleScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesStaticBoundClassAsTransient(): void
    {
        $container = Container::get();
        $container->bind(ClassC::class)->toSelf()->isTransientScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testIfContainerResolvesTokenBoundClassAsSingleton(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toClass(ClassC::class)->isSingleScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesTokenBoundClassAsTransient(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toClass(ClassC::class)->isTransientScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testIfContainerResolvesLazyClassAsSingleton(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toLazyClass(ClassC::class)->isSingleScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
        $sum = $instance1->add(5, 10);
        $this->assertEquals(15, $sum);
        $sum = $instance2->add(5, 12);
        $this->assertEquals(17, $sum);
    }

    public function testIfContainerResolvesLazyClassAsTransient(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toLazyClass(ClassC::class)->isTransientScoped();
        $container->bind("A")->toConstantValue("Hello World");
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
        $sum = $instance1->add(5, 10);
        $this->assertEquals(15, $sum);
        $sum = $instance2->add(5, 12);
        $this->assertEquals(17, $sum);
    }

    public function testIfContainerResolvesLazyFactoryAsSingleton(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toLazyFactory(function() {
            return new ClassC("Hello World");
        })->isSingleScoped();
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesLazyFactoryAsTransient(): void
    {
        $container = Container::get();
        $container->bind("ClassC")->toLazyFactory(function() {
            return new ClassC("Hello World");
        })->isTransientScoped();
        $instance1 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }
}
