<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PluginName
{
    public function __construct(public string $value)
    {
    }
}
