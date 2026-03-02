<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PluginVersion
{
    public function __construct(public string $value)
    {
    }
}
