<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Exceptions\NetworkException;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\HttpClient;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\HttpClientResponse;

class HttpClientAdapter implements HttpClient
{
    /**
     * @inheritDoc
     */
    public function delete(string $url, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('DELETE', $url, $params, $headers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $url, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('GET', $url, $params, $headers);
    }

    /**
     * @inheritDoc
     */
    public function head(string $url, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('HEAD', $url, $params, $headers);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('PATCH', $url, $params, $headers, $body);
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('POST', $url, $params, $headers, $body);
    }

    /**
     * @inheritDoc
     */
    public function put(string $url, mixed $body, array $params = [], array $headers = []): HttpClientResponse
    {
        return $this->sendHttpRequest('PUT', $url, $params, $headers, $body);
    }

    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     * @throws NetworkException
     */
    private function sendHttpRequest(
        string $method,
        string $url,
        array $params = [],
        array $headers = [],
        mixed $body = null
    ): HttpClientResponse {
        $args = [
            'method' => strtoupper($method),
            'headers' => $headers,
            'body' => $body,
        ];

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            throw new NetworkException("HTTP request failed: " . $response->get_error_message());
        }
        return new HttpClientResponseAdapter($response);
    }
}
