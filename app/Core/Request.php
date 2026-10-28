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
        if (is_string($contentType) && str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->jsonBody = $decoded;
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper((string) $this->server('REQUEST_METHOD', 'GET'));
    }

    public function getPath(): string
    {
        $uri = (string) $this->server('REQUEST_URI', '/');
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        return '/' . trim($uri, '/');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $this->getParams)) {
            return $default;
        }
        $val = $this->getParams[$key];
        // If default is string and value is array (e.g. ?search[]=x), prevent TypeError by returning default
        if (is_string($default) && is_array($val)) {
            return $default;
        }
        return $val;
    }

    public function getString(string $key, string $default = ''): string
    {
        $val = $this->get($key, $default);
        if (is_array($val)) {
            return $default;
        }
        return trim((string) $val);
    }

    public function getInt(string $key, int $default = 0): int
    {
        $val = $this->get($key, $default);
        if (is_array($val) || !is_numeric($val)) {
            return $default;
        }
        return (int) $val;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            $val = $this->jsonBody[$key];
            if (is_string($default) && is_array($val)) {
                return $default;
            }
            return $val;
        }

        if (!array_key_exists($key, $this->postParams)) {
            return $default;
        }

        $val = $this->postParams[$key];
        if (is_string($default) && is_array($val)) {
            return $default;
        }
        return $val;
    }

    public function postString(string $key, string $default = ''): string
    {
        $val = $this->post($key, $default);
        if (is_array($val)) {
            return $default;
        }
        return trim((string) $val);
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

    public function header(string $name, ?string $default = null): ?string
    {
        $formatted = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($this->serverParams[$formatted])) {
            return (string) $this->serverParams[$formatted];
        }
        if (isset($this->serverParams[strtoupper(str_replace('-', '_', $name))])) {
            return (string) $this->serverParams[strtoupper(str_replace('-', '_', $name))];
        }
        return $default;
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isAjax(): bool
    {
        $requestedWith = $this->header('X-Requested-With', '');
        $accept = (string) $this->server('HTTP_ACCEPT', '');
        return strtolower($requestedWith ?? '') === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }
}
