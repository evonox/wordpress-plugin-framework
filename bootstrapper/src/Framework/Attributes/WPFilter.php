<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class WPFilter
{
    public function __construct(public string $filterName, public int $priority = 10)
    {
    }
}
