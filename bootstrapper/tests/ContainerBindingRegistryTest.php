<?php

namespace Tests;

use __PLUGIN__\Framework\DI\ContainerBindingKind;
use __PLUGIN__\Framework\DI\ContainerBindingRegistry;
use __PLUGIN__\Framework\DI\ContainerScopeKind;
use PHPUnit\Framework\TestCase;

class ContainerBindingRegistryTest extends TestCase
{
    public function testIdentifierIsBounded(): void
    {
        $container = new ContainerBindingRegistry();
        $container->bindIdentifier(
            "token",
            "Clazz",
            ContainerBindingKind::ConstantValue,
            ContainerScopeKind::Singleton
        );
        $this->assertTrue($container->isIdentifierBound("token"));
        $this->assertFalse($container->isIdentifierBound("token2"));
    }

    public function testIdentifierIsUnbinded(): void
    {
        $container = new ContainerBindingRegistry();
        $container->bindIdentifier(
            "token",
            "Clazz",
            ContainerBindingKind::ConstantValue,
            ContainerScopeKind::Singleton
        );
        $this->assertTrue($container->isIdentifierBound("token"));
        $container->unbindIdentifier("token");
        $this->assertFalse($container->isIdentifierBound("token"));
    }

    public function testIfIdentiferValueIsReturned(): void
    {
        $container = new ContainerBindingRegistry();
        $this->assertFalse($container->getValueForIdentifier("token"));
        $container->bindIdentifier(
            "token",
            "Clazz",
            ContainerBindingKind::ConstantValue,
            ContainerScopeKind::Singleton
        );
        $this->assertEquals("Clazz", $container->getValueForIdentifier("token"));
    }

    public function testIfIdentiferBindingKindIsReturned(): void
    {
        $container = new ContainerBindingRegistry();
        $this->assertFalse($container->getBindingKindForIdentifier("token"));
        $container->bindIdentifier(
            "token",
            "Clazz",
            ContainerBindingKind::ConstantValue,
            ContainerScopeKind::Singleton
        );
        $this->assertEquals(
            ContainerBindingKind::ConstantValue,
            $container->getBindingKindForIdentifier("token")
        );
    }

    public function testIfContainerScopeKindIsReturned(): void
    {
        $container = new ContainerBindingRegistry();
        $this->assertFalse($container->getScopeForIdentifier("token"));
        $container->bindIdentifier(
            "token",
            "Clazz",
            ContainerBindingKind::ConstantValue,
            ContainerScopeKind::Singleton
        );
        $this->assertEquals(
            ContainerScopeKind::Singleton,
            $container->getScopeForIdentifier("token")
        );
    }
}
