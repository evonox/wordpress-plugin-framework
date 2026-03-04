<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

interface HttpClient
{
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function head(string $url, array $params = [], array $headers = []): HttpClientResponse;
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function get(string $url, array $params = [], array $headers = []): HttpClientResponse;
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function post(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse;
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function patch(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse;
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function put(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse;
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function delete(string $url, array $params = [], array $headers = []): HttpClientResponse;
}
