<?php
/**
 * Zebra Tours Moçambique - Ponto de Entrada Front-Controller PHP
 * PHP 8.2+ MVC Architecture
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Autoloader PSR-4 simples caso o composer autoload não tenha sido gerado
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

try {
    $request = new Request();
    $router = new Router();

    // Carregar rotas
    require BASE_PATH . '/routes/web.php';

    $response = $router->dispatch($request);
    $response->send();
} catch (\Throwable $e) {
    http_response_code(500);
    if (getenv('APP_ENV') === 'development') {
        echo '<h1>500 - Erro no Servidor</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 - Erro no Servidor</h1><p>Pedimos desculpa, ocorreu um erro inesperado. Por favor tente mais tarde.</p>';
    }
}
