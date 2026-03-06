<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

interface OptionsAPI
{
    public function hasOption(string $optionName): bool;
    public function getOption(string $optionName, mixed $default = null): mixed;
    public function setOption(string $optionName, mixed $value, bool $autoload = false): void;
    public function deleteOption(string $optionName): void;

    public function hasSiteOption(string $optionName): bool;
    public function getSiteOption(string $optionName, mixed $default = null): mixed;
    public function setSiteOption(string $optionName, mixed $value): void;
    public function deleteSiteOption(string $optionName): void;

    public function prefixOptionName(string $optionName): string;
}
