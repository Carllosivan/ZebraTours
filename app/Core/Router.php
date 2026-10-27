<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        $normalizedPath = '/' . trim($path, '/');
        if ($normalizedPath === '//') $normalizedPath = '/';

        // Convert {param} to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $normalizedPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $methodMatched = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                if ($route['method'] !== $method) {
                    $methodMatched = true;
                    continue;
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    return $controller->$action($request, $params);
                }

                if (is_callable($handler)) {
                    return $handler($request, $params);
                }
            }
        }

        if ($methodMatched) {
            return new Response('<h1>405 - Método Não Permitido</h1>', 405);
        }

        return new Response('<h1>404 - Página Não Encontrada</h1><p>A página que procura não existe.</p>', 404);
    }
}
