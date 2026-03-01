<?php

namespace Tests\Helper;

use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\Attributes\PluginPrefix;
use __PLUGIN__\Framework\Attributes\WPAction;

#[PluginPrefix("1.0")]
class ClassA
{
    #[Inject("pluginVersion")]
    public string $pluginVersion;

    public function __construct(#[Inject("pluginVersion")] string $pluginVersion)
    {
        $this->pluginVersion = $pluginVersion;
    }

    #[WPAction("some_hook")]
    public function method(ClassA $param): void
    {
    }
}
