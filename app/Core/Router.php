<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): void
    {
        $normalized = '/' . trim($path, '/');
        // Convert route parameters {slug} or {id} into regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $normalized);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'      => strtoupper($method),
            'pattern'     => $pattern,
            'original'    => $normalized,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // Extract named capture groups
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $params[$key] = $val;
                    }
                }

                // Check middlewares
                foreach ($route['middlewares'] as $mw) {
                    if ($mw === 'auth') {
                        if (!Auth::check()) {
                            Session::setFlash('error', 'Por favor, inicie sessão para aceder à área de administração.');
                            return Response::redirect('/admin/login');
                        }
                    }
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    $result = call_user_func($handler, $request, $params);
                } elseif (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    $result = $controller->$action($request, $params);
                } else {
                    return Response::html("<h1>Erro no Handler da Rota</h1>", 500);
                }

                if ($result instanceof Response) {
                    return $result;
                } elseif (is_string($result)) {
                    return Response::html($result);
                } elseif (is_array($result)) {
                    return Response::json($result);
                }

                return new Response();
            }
        }

        // 404 Not Found
        $notFoundHtml = View::render('errors/404', [
            'pageTitle' => 'Página não encontrada | Zebra Tours',
            'path'      => $path,
        ]);
        return Response::html($notFoundHtml, 404);
    }
}
