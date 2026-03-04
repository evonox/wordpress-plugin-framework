<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

use SimpleXMLElement;

interface HttpClientResponse
{
    public function getStatusCode(): int;
    /**
     * @return array<string, string|array<string>>
     */
    public function getHeaders(): array;
    /**
     * @return null|string|array<string>
     */
    public function getHeader(string $headerName): null|string|array;
    public function getResponseBody(): string;
    public function asJson(bool $objectsAsArray = false): mixed;
    public function asXml(): SimpleXMLElement;
}
