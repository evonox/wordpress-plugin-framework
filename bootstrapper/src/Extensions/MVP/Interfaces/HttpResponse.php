<?php

namespace __PLUGIN__\Extensions\MVP\Interfaces;

interface HttpResponse
{
    public function setStatusCode(int $code): void;
    public function setHeader(string $name, string $value): void;
    public function addHeader(string $name, string $value): void;
    public function removeHeader(string $name): void;
    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = false
    ): void;
    public function removeCookie(string $name): void;
    public function setContentType(string $type, string $charset): void;
    public function setBody(string $content): void;
    public function send(): void;
    public function redirect(string $url, int $statusCode = 302): void;
    public function downloadFile(string $filePath, string $fileName, string $mime): void;
}
