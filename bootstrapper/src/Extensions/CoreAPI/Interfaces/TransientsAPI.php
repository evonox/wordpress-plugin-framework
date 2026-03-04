<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

interface TransientsAPI
{
    public function getTransient(string $transientName): mixed;
    public function setTransient(string $transientName, mixed $value, int $expirationInSeconds = 0): void;
    public function deleteTransient(string $transientName): void;
    public function getSiteTransient(string $transientName): mixed;
    public function setSiteTransient(string $transientName, mixed $value, int $expirationInSeconds = 0): void;
    public function deleteSiteTransient(string $transientName): void;
    public function prefixTransientName(string $transientName): string;
}
