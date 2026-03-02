<?php

namespace __PLUGIN__\Framework\Helpers;

class CaseHelper
{
    public static function toSnakeCase(string $input): string
    {
        $pattern = '/(?<!^)[A-Z]/';
        $replacement = '_$0';
        return strtolower(preg_replace($pattern, $replacement, $input));
    }
}
