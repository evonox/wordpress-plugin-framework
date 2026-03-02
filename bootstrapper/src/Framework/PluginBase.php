<?php

namespace __PLUGIN__\Framework;

use __PLUGIN__\Framework\DI\Container;
use __PLUGIN__\Framework\Helpers\ExtensionsHelper;
use __PLUGIN__\Framework\Helpers\ReflectionHelper;
use __PLUGIN__\Framework\Services\PluginService;
use __PLUGIN__\Framework\Attributes\PluginPrefix;

abstract class PluginBase extends PluginService
{
    abstract public function onActivate(): void;
    abstract public function onDeactivate(): void;
    abstract public function onUninstall(): void;

    abstract protected function getServiceContainerPath(): string;

    public function boot(): void
    {
        // 1. Check the plugin has defined its Prefix
        $pluginPrefixAttr = ReflectionHelper::getClassAttribute(static::class, PluginPrefix::class);
        if ($pluginPrefixAttr === false) {
            throw new \Exception("PluginMain Class is missing 'PluginPrefix' attribute.");
        }
        $pluginPrefix = $pluginPrefixAttr->value;

        // 2. Initialize the DI container and bind the plugin prefix
        $container = Container::get();
        $container->bind("PluginPrefix")->toConstantValue($pluginPrefix);

        // 3. Boot framework extensions
        ExtensionsHelper::bootstrapExtensions();

        // 4. Boot the plugin and its registered services
        self::bootService();
        // $this->bootPluginServices();
    }

    // @phpstan-ignore method.unused
    private function bootPluginServices(): void
    {
        $serviceContainerPath = $this->getServiceContainerPath();
        $serviceNames = include $serviceContainerPath;

        foreach ($serviceNames as $serviceName) {
            if (method_exists($serviceName, "bootService")) {
                call_user_func([$serviceName, "bootService"]);
            } else {
                trigger_error("Service '$serviceName' does not have static 'bootService' method.", E_USER_WARNING);
            }
        }
    }
}
