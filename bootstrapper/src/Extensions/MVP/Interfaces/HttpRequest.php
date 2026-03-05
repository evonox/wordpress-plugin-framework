<?php

namespace __PLUGIN__\Extensions\MVP\Interfaces;

interface HttpRequest
{
    public function getMethod(): string;
    public function isSecure(): bool;
    public function getRemoteAddr(): string;
    public function getUri(): string;
    /**
     * @return array<string, string[]|string>
     */
    public function getHeaders(): array;
    /**
     * @return array<string, string[]|string|null>
     */
    public function getHeader(string $name): array|string|null;
    public function getQueryParam(string $name): string|null;
    public function getPostParam(string $name): string|null;
    public function hasCookie(string $name): bool;
    public function getCookie(string $name): string|null;
    /**
     * @return array<string, string>
     */
    public function getCookies(): array;
    public function isAjax(): bool;
}
