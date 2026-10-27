<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $getParams;
    private array $postParams;
    private array $serverParams;
    private array $cookies;
    private array $files;
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->serverParams = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;

        $contentType = $this->server('CONTENT_TYPE', '');
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->jsonBody = $decoded;
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper($this->server('REQUEST_METHOD', 'GET'));
    }

    public function getPath(): string
    {
        $uri = $this->server('REQUEST_URI', '/');
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        return '/' . trim($uri, '/');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getParams[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        return $this->postParams[$key] ?? $default;
    }

    public function all(): array
    {
        if ($this->jsonBody !== null) {
            return array_merge($this->getParams, $this->jsonBody);
        }
        return array_merge($this->getParams, $this->postParams);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->serverParams[$key] ?? $default;
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isAjax(): bool
    {
        return $this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest' ||
               str_contains($this->server('HTTP_ACCEPT', ''), 'application/json');
    }
}
