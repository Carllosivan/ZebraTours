<?php
/**
 * Zebra Tours Moçambique - Front Controller
 * PHP 8.2+ MVC Architecture with HTML & Tailwind CSS
 */

declare(strict_types=1);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Define constants
defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));
defined('PUBLIC_PATH') || define('PUBLIC_PATH', __DIR__);

// Serve existing static files in PHP CLI server
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($uri && $uri !== '/' && is_file(PUBLIC_PATH . $uri)) {
        return false;
    }
}

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';

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

// Require global helper functions
require ROOT_PATH . '/app/Core/helpers.php';

// Start session
\App\Core\Session::start();

// Initialize HTTP Request & Router
$request = new \App\Core\Request();
$router = new \App\Core\Router();

// Load Web Routes
require ROOT_PATH . '/routes/web.php';

// Dispatch Request
try {
    $response = $router->dispatch($request);
    $response->send();
} catch (\Throwable $e) {
    error_log('[Zebra Tours Error]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8"><title>Erro no Servidor</title></head><body style="font-family:sans-serif;padding:2rem;text-align:center;">';
    echo '<h1>Ocorreu um erro temporário no servidor</h1>';
    echo '<p>Por favor, tente novamente dentro de instantes ou contacte o suporte da Zebra Tours.</p>';
    echo '<a href="/">Voltar à Página Inicial</a>';
    echo '</body></html>';
}
