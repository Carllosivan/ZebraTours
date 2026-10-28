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

        // 1. Central CSRF Validation for all POST requests
        if ($method === 'POST') {
            $token = $request->post('_csrf');
            if (empty($token)) {
                $token = $request->header('X-CSRF-Token');
            }

            if (!Session::validateCsrfToken($token ? (string) $token : null)) {
                if ($request->isAjax()) {
                    return Response::json([
                        'success' => false,
                        'error'   => 'Sessão de formulário expirada (CSRF inválido). Por favor recarregue a página.',
                        'code'    => 419,
                    ], 419);
                }

                $csrfErrorHtml = '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8">'
                    . '<title>419 Sessão Expirada | Zebra Tours</title>'
                    . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                    . '<script src="https://cdn.tailwindcss.com"></script></head>'
                    . '<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6">'
                    . '<div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center space-y-6 shadow-2xl">'
                    . '<div class="w-16 h-16 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold">419</div>'
                    . '<h1 class="text-2xl font-bold tracking-tight">Sessão Expirada</h1>'
                    . '<p class="text-sm text-slate-400 leading-relaxed">O token de segurança da sua sessão expirou por inatividade ou é inválido. Por favor, volte atrás e recarregue a página antes de submeter novamente.</p>'
                    . '<a href="javascript:history.back()" class="inline-block px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider transition">Voltar e Tentar Novamente</a>'
                    . '</div></body></html>';

                return Response::html($csrfErrorHtml, 419);
            }
        }

        // 2. Match Route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && !($method === 'HEAD' && $route['method'] === 'GET')) {
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

                $isAuthProtected = false;

                // Check middlewares
                foreach ($route['middlewares'] as $mw) {
                    if ($mw === 'auth') {
                        $isAuthProtected = true;
                        if (!Auth::check()) {
                            Session::setFlash('error', 'Por favor, inicie sessão para aceder à área de administração.');
                            return Response::redirect('/admin/login');
                        }
                    } elseif (str_starts_with($mw, 'role:')) {
                        $requiredRole = substr($mw, 5);
                        if (!Auth::check()) {
                            return Response::redirect('/admin/login');
                        }
                        if ($requiredRole === 'admin' && !Auth::hasRole(['admin', 'superadmin'])) {
                            return Response::html("<h1>403 Acesso Negado</h1><p>Não tem permissão suficiente para esta ação.</p>", 403);
                        } elseif ($requiredRole === 'superadmin' && !Auth::hasRole('superadmin')) {
                            return Response::html("<h1>403 Acesso Negado</h1><p>Apenas o Superadministrador pode realizar esta operação.</p>", 403);
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

                $response = $result instanceof Response
                    ? $result
                    : (is_string($result)
                        ? Response::html($result)
                        : (is_array($result)
                            ? Response::json($result)
                            : new Response()));

                // Cache-Control headers on auth protected pages
                if ($isAuthProtected) {
                    $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
                    $response->setHeader('Pragma', 'no-cache');
                }

                return $response;
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
