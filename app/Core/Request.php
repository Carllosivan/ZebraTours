<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Representa a requisição HTTP recebida na arquitetura MVC.
 * Encapsula parâmetros de consulta (GET), corpo (POST) e ficheiros enviados (FILES).
 */
class Request
{
    private string $method;
    private string $uri;
    private array $params;
    private array $body;
    private array $files;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $parts = explode('?', $rawUri);
        $this->uri = '/' . trim($parts[0], '/');
        if ($this->uri === '//') {
            $this->uri = '/';
        }
        $this->params = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function get(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Retorna os dados de um ficheiro enviado via $_FILES
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Retorna todos os ficheiros enviados
     */
    public function files(): array
    {
        return $this->files;
    }

    public function all(): array
    {
        return array_merge($this->params, $this->body);
    }
}
