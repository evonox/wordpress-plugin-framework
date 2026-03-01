<?php

namespace Tests;

use __PLUGIN__\Framework\DI\BindingFluentBuilder;
use __PLUGIN__\Framework\DI\ContainerBindingKind;
use __PLUGIN__\Framework\DI\ContainerBindingRegistry;
use __PLUGIN__\Framework\DI\ContainerScopeKind;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ClassA;

class BindingFluentBuilderTest extends TestCase
{
    public function testClassIsBoundToSelf(): void
    {
        $registry = new ContainerBindingRegistry();
        new BindingFluentBuilder($registry, ClassA::class)->toSelf()->isSingleScoped();
        $this->assertTrue($registry->isIdentifierBound(ClassA::class));
        $this->assertEquals(ClassA::class, $registry->getValueForIdentifier(ClassA::class));
        $this->assertEquals(ContainerBindingKind::Clazz, $registry->getBindingKindForIdentifier(ClassA::class));
        $this->assertEquals(ContainerScopeKind::Singleton, $registry->getScopeForIdentifier(ClassA::class));
    }

    public function testTokenIsBoundToClass(): void
    {
        $registry = new ContainerBindingRegistry();
        new BindingFluentBuilder($registry, 'classA')->toClass(ClassA::class)->isSingleScoped();
        $this->assertTrue($registry->isIdentifierBound('classA'));
        $this->assertEquals(ClassA::class, $registry->getValueForIdentifier('classA'));
        $this->assertEquals(ContainerBindingKind::Clazz, $registry->getBindingKindForIdentifier('classA'));
        $this->assertEquals(ContainerScopeKind::Singleton, $registry->getScopeForIdentifier('classA'));
    }

    public function testTokenIsBoundToLazyClass(): void
    {
        $registry = new ContainerBindingRegistry();
        new BindingFluentBuilder($registry, 'classA')->toLazyClass(ClassA::class)->isTransientScoped();
        $this->assertTrue($registry->isIdentifierBound('classA'));
        $this->assertEquals(ClassA::class, $registry->getValueForIdentifier('classA'));
        $this->assertEquals(ContainerBindingKind::LazyClazz, $registry->getBindingKindForIdentifier('classA'));
        $this->assertEquals(ContainerScopeKind::Transient, $registry->getScopeForIdentifier('classA'));
    }

    public function testTokenIsBoundToConstantValue(): void
    {
        $registry = new ContainerBindingRegistry();
        new BindingFluentBuilder($registry, 'constantValue')->toConstantValue(42);
        $this->assertTrue($registry->isIdentifierBound('constantValue'));
        $this->assertEquals(42, $registry->getValueForIdentifier('constantValue'));
        $this->assertEquals(
            ContainerBindingKind::ConstantValue,
            $registry->getBindingKindForIdentifier('constantValue')
        );
        $this->assertEquals(ContainerScopeKind::Singleton, $registry->getScopeForIdentifier('constantValue'));
    }

    public function testTokenIsBoundToDynamicValue(): void
    {
        $registry = new ContainerBindingRegistry();
        $lambda = function (): int {
            return 42;
        };
        new BindingFluentBuilder($registry, 'dynamicValue')->toDynamicValue($lambda);
        $this->assertTrue($registry->isIdentifierBound('dynamicValue'));
        $this->assertEquals($lambda, $registry->getValueForIdentifier('dynamicValue'));
        $this->assertEquals(
            ContainerBindingKind::DynamicValue,
            $registry->getBindingKindForIdentifier('dynamicValue')
        );
        $this->assertEquals(ContainerScopeKind::Singleton, $registry->getScopeForIdentifier('dynamicValue'));
    }

    public function testTokenIsBoundToLazyFactory(): void
    {
        $registry = new ContainerBindingRegistry();
        $lambda = function (): int {
            return 42;
        };
        new BindingFluentBuilder($registry, 'lazyFactory')->toLazyFactory($lambda)->isTransientScoped();
        $this->assertTrue($registry->isIdentifierBound('lazyFactory'));
        $this->assertEquals($lambda, $registry->getValueForIdentifier('lazyFactory'));
        $this->assertEquals(
            ContainerBindingKind::LazyFactory,
            $registry->getBindingKindForIdentifier('lazyFactory')
        );
        $this->assertEquals(ContainerScopeKind::Transient, $registry->getScopeForIdentifier('lazyFactory'));
    }
}
