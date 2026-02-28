<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute]
class WPFilter
{
    public function __construct(public string $filterName, public int $priority = 10)
    {
    }
}
