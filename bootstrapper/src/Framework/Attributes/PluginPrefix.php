<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute]
class PluginPrefix
{
    public function __construct(public string $value)
    {
    }
}
