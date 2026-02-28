<?php

namespace __PLUGIN__\Plugin;

use __PLUGIN__\Framework\Attributes\PluginPrefix;
use __PLUGIN__\Framework\PluginBase;

#[PluginPrefix("")]
class PluginMain extends PluginBase
{
    public function onActivate(): void
    {
    }

    public function onDeactivate(): void
    {
    }

    public function onUninstall(): void
    {
    }

    protected function getServiceContainerPath(): string
    {
        return __DIR__ . "/services.php";
    }
}
