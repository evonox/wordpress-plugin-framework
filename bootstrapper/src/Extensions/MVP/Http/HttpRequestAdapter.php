<?php

namespace __PLUGIN__\Extensions\MVP\Http;

use __PLUGIN__\Extensions\MVP\Interfaces\HttpRequest;

class HttpRequestAdapter implements HttpRequest
{
    /**
     * @inheritDoc
     */
    public function hasCookie(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
    /**
     * @inheritDoc
     */
    public function getCookie(string $name): string|null
    {
        if (isset($_COOKIE[$name])) {
            $cookieValue = wp_unslash($_COOKIE[$name]);
            $cookieValue = sanitize_text_field($cookieValue);
            return $cookieValue;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCookies(): array
    {
        $cookieNames = array_keys($_COOKIE);
        $cookies = [];
        foreach ($cookieNames as $name) {
            $cookies[$name] = $this->getCookie($name);
        }
        return $cookies;
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $name): array|string|null
    {
        $headers = getallheaders();
        return $headers[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return getallheaders();
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @inheritDoc
     */
    public function getPostParam(string $name): string|null
    {
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
            $value = wp_unslash($value);
            $value = sanitize_text_field($value);
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getQueryParam(string $name): string|null
    {
        if (isset($_GET[$name])) {
            $value = $_GET[$name];
            $value = wp_unslash($value);
            $value = sanitize_text_field($value);
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getParam(string $name): string|null
    {
        $method = $this->getMethod();
        if (in_array($method, ["GET", "DELETE"])) {
            return $this->getQueryParam($name);
        } elseif (in_array($method, ["POST","PUT", "PATCH"])) {
            return $this->getPostParam($name);
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRemoteAddr(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @inheritDoc
     */
    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? "";
        $uri = sanitize_url($uri);
        return $uri;
    }

    /**
     * @inheritDoc
     */
    public function isAjax(): bool
    {
        return wp_doing_ajax();
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return is_ssl();
    }
}
