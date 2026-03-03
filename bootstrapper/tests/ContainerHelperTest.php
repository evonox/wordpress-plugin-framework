<?php

namespace Tests;

use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ClassC;

class ContainerHelperTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testIfContainerBindsAndResolves(): void
    {
        $this->container->bind('test_constant')->toConstantValue(42);
        $this->assertTrue($this->container->isBound('test_constant'));
        $this->assertEquals(42, $this->container->resolve('test_constant'));

        $this->container->unbind('test_constant');
        $this->assertFalse($this->container->isBound('test_constant'));
    }

    public function testIfContainerRebindsAndResolves(): void
    {
        $this->container->bind('test_rebind')->toConstantValue(42);
        $this->assertEquals(42, $this->container->resolve('test_rebind'));

        $this->container->rebind('test_rebind')->toConstantValue(100);
        $this->assertEquals(100, $this->container->resolve('test_rebind'));
    }

    public function testIfContainerResolvesConstantValueAsSingleton(): void
    {
        $this->container->bind('test_singleton')->toConstantValue(new \stdClass());
        $instance1 = $this->container->resolve('test_singleton');
        $instance2 = $this->container->resolve('test_singleton');
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContinaerResolvesDynamicValueAsSingleton(): void
    {
        $this->container->bind('test_dynamic_singleton')->toDynamicValue(function () {
            return new \stdClass();
        });
        $instance1 = $this->container->resolve('test_dynamic_singleton');
        $instance2 = $this->container->resolve('test_dynamic_singleton');
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesSelfBoundClassAsSingleton(): void
    {
        $this->container->bind(ClassC::class)->toSelf()->isSingleScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesStaticBoundClassAsTransient(): void
    {
        $this->container->bind(ClassC::class)->toSelf()->isTransientScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve(ClassC::class);
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testIfContainerResolvesTokenBoundClassAsSingleton(): void
    {
        $this->container->bind("ClassC")->toClass(ClassC::class)->isSingleScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesTokenBoundClassAsTransient(): void
    {
        $this->container->bind("ClassC")->toClass(ClassC::class)->isTransientScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testIfContainerResolvesLazyClassAsSingleton(): void
    {
        $this->container->bind("ClassC")->toLazyClass(ClassC::class)->isSingleScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
        $sum = $instance1->add(5, 10);
        $this->assertEquals(15, $sum);
        $sum = $instance2->add(5, 12);
        $this->assertEquals(17, $sum);
    }

    public function testIfContainerResolvesLazyClassAsTransient(): void
    {
        $this->container->bind("ClassC")->toLazyClass(ClassC::class)->isTransientScoped();
        $this->container->bind("A")->toConstantValue("Hello World");
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
        $sum = $instance1->add(5, 10);
        $this->assertEquals(15, $sum);
        $sum = $instance2->add(5, 12);
        $this->assertEquals(17, $sum);
    }

    public function testIfContainerResolvesLazyFactoryAsSingleton(): void
    {
        $this->container->bind("ClassC")->toLazyFactory(function () {
            return new ClassC("Hello World");
        })->isSingleScoped();
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertSame($instance1, $instance2);
    }

    public function testIfContainerResolvesLazyFactoryAsTransient(): void
    {
        $this->container->bind("ClassC")->toLazyFactory(function () {
            return new ClassC("Hello World");
        })->isTransientScoped();
        $instance1 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance1->a);
        $instance2 = $this->container->resolve("ClassC");
        $this->assertEquals("Hello World", $instance2->a);
        $this->assertNotSame($instance1, $instance2);
    }
}
