<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute]
class WPAction
{
    public function __construct(public string $actionName, public int $priority = 10)
    {
    }
}
