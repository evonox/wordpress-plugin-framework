<?php

namespace __PLUGIN__\Framework;

use __PLUGIN__\Framework\DI\Container;
use __PLUGIN__\Framework\Helpers\CaseHelper;
use __PLUGIN__\Framework\Helpers\ExtensionsHelper;
use __PLUGIN__\Framework\Helpers\ReflectionHelper;
use __PLUGIN__\Framework\Services\PluginService;
use __PLUGIN__\Framework\Attributes\PluginPrefix;

abstract class PluginBase extends PluginService
{
    abstract public function onActivate(): void;
    abstract public function onDeactivate(): void;
    abstract public function onUninstall(): void;

    protected function getServiceContainerPath(): string
    {
        return dirname(__PLUGIN___MAIN_FILE_PATH) . "/" . "plugin-services.php";
    }

    public function boot(): void
    {
        // 1. Check the plugin has defined its Prefix
        $pluginPrefixAttr = ReflectionHelper::getClassAttribute(static::class, PluginPrefix::class);
        if ($pluginPrefixAttr === false) {
            throw new \Exception("PluginMain Class is missing 'PluginPrefix' attribute.");
        }
        $pluginPrefix = CaseHelper::toSnakeCase($pluginPrefixAttr->value);

        // 2. Initialize the DI container and bind the plugin prefix
        $container = Container::get();
        $container->bind("
        ")->toConstantValue($pluginPrefix);

        // 3. Boot framework extensions
        ExtensionsHelper::bootstrapExtensions();

        // 4. Boot the plugin and its registered services
        self::bootService();
        $this->bootPluginServices();
    }

    private function bootPluginServices(): void
    {
        $serviceContainerPath = $this->getServiceContainerPath();
        if (! file_exists($serviceContainerPath)) {
            trigger_error("File '$serviceContainerPath' not found", E_USER_WARNING);
            return;
        }
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
