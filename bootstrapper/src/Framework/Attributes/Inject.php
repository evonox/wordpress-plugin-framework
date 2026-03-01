<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute]
class Inject
{
    public function __construct(public string $identifier)
    {
    }
}
