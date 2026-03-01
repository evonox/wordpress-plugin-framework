<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class WPAction
{
    public function __construct(public string $actionName, public int $priority = 10)
    {
    }
}
