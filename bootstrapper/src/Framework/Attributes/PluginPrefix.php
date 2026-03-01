<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PluginPrefix
{
    public function __construct(public string $value)
    {
    }
}
