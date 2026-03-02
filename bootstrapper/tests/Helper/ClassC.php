<?php

namespace Tests\Helper;

use __PLUGIN__\Framework\Attributes\Inject;

class ClassC
{
    public function __construct(#[Inject("A")] public string $a)
    {
    }

    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}
