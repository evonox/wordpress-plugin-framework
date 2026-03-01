<?php

namespace Tests\Helper;

use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\Attributes\PostConstruct;

class ClassB
{
    #[Inject("PluginVersion")]
    public string $pluginVersion;

    #[Inject()]
    public ClassA $classA;

    public function __construct(
        #[Inject("PluginVersion")] string $pluginVersion,
        ClassA $classA,
    ) {
        $this->pluginVersion = $pluginVersion;
        $this->classA = $classA;
    }

    #[PostConstruct()]
    public function handlePostConstruct(): void
    {
    }
}
