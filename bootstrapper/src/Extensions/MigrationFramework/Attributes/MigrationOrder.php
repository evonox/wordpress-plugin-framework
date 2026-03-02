<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class MigrationOrder
{
    public function __construct(public int $value)
    {
    }
}
