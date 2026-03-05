<?php

namespace __PLUGIN__\Extensions\MVP\Http;

use __PLUGIN__\Extensions\MVP\Interfaces\HttpResponse;

class HttpResponseAdapter implements HttpResponse
{
    private int $statusCode = 200;
    /**
     * @var array<string, string[]>
     */
    private array $headers = [];
    /**
     * @var array<string, mixed>
     */
    private array $cookies = [];
    private string $body = "";
    /**
     * @inheritDoc
     */
    public function addHeader(string $name, string $value): void
    {
        if (!isset($this->headers[$name])) {
            $this->headers[$name] = [];
        }
        $this->headers[$name][] = $value;
    }

    /**
     * @inheritDoc
     */
    public function downloadFile(string $filePath, string $fileName, string $mime): void
    {
        if (file_exists($filePath) === false) {
            throw new \Exception("File not found.");
        }
        $fileSize = filesize($filePath);

        $this->setContentType($mime, null);
        $this->setHeader("Content-Disposition", "attachment; filename=\"$fileName\"");
        $this->setHeader("Content-Length", "$fileSize");

        http_response_code($this->statusCode);
        $this->writeHttpHeaders();
        readfile($filePath);
        exit;
    }

    /**
     * @inheritDoc
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $url = sanitize_url($url);
        $url = $this->sanitizeHeaderValue($url);
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * @inheritDoc
     */
    public function removeCookie(string $name): void
    {
        if (isset($this->cookies[$name])) {
            unset($this->cookies[$name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function removeHeader(string $name): void
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        $this->writeHttpHeaders();
        echo $this->body;
        exit;
    }

    /**
     * @inheritDoc
     */
    public function setBody(string $content): void
    {
        $this->body = $content;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $type, ?string $charset): void
    {
        $headerValue = $charset === null ? $type : $type . "; charset=" . $charset;
        $this->setHeader("Content-Type", $headerValue);
    }

    /**
     * @inheritDoc
     */
    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httpOnly = true
    ): void {
        $this->cookies[$name] = [
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httpOnly' => $httpOnly
        ];
    }

    /**
     * @inheritDoc
     */
    public function setHeader(string $name, string $value): void
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        } else {
            $this->addHeader($name, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    private function writeHttpHeaders(): void
    {
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                $value = $this->sanitizeHeaderValue($value);
                header($name . ': ' . $value);
            }
        }

        foreach ($this->cookies as $name => $cookie) {
            setcookie(
                $name,
                $cookie["value"],
                time() + $cookie["expires"],
                $cookie["path"],
                $cookie["domain"],
                $cookie["secure"],
                $cookie["httpOnly"]
            );
        }
    }

    private function sanitizeHeaderValue(string $value): string
    {
        $value = str_replace(["\r", "\n"], '', $value);
        $value = preg_replace('/[^\x20-\x7E]/', '', $value);
        return $value;
    }
}
