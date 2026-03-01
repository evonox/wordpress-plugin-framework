<?php

namespace __PLUGIN__\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Inject
{
    public function __construct(public string $identifier)
    {
    }
}
