<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Exceptions\SerializationException;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\HttpClientResponse;
use SimpleXMLElement;

class HttpClientResponseAdapter implements HttpClientResponse
{
    /**
     * @param array<string, mixed> $response
     */
    public function __construct(private array $response)
    {
    }

    /**
     * @inheritDoc
     */
    public function asJson(bool $objectsAsArray = false): mixed
    {
        $responseBody = $this->getResponseBody();
        $decoded = json_decode($responseBody, !$objectsAsArray);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SerializationException("Failed to decode JSON response: " . json_last_error_msg());
        }
        return $decoded;
    }

    /**
     * @inheritDoc
     */
    public function asXml(): SimpleXMLElement
    {
        $responseBody = $this->getResponseBody();
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($responseBody);
        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMessage = "Failed to parse XML response: ";
            foreach ($errors as $error) {
                $errorMessage .= trim($error->message) . "; ";
            }
            libxml_clear_errors();
            throw new SerializationException($errorMessage);
        }
        return $xml;
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $headerName): null|string|array
    {
        $headerValue = wp_remote_retrieve_header($this->response, $headerName);
        return $headerValue !== '' ? $headerValue : null;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        $headers = wp_remote_retrieve_headers($this->response);
        return $headers->getAll();
    }

    /**
     * @inheritDoc
     */
    public function getResponseBody(): string
    {
        return wp_remote_retrieve_body($this->response);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return wp_remote_retrieve_response_code($this->response);
    }
}
