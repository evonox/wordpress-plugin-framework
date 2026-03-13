<?php

namespace __PLUGIN__\Extensions\MVP\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Get
{
    public function __construct(public string $action)
    {
    }
}
