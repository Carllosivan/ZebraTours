<?php
/**
 * Zebra Tours Moçambique - Front Controller
 * PHP 8.2+ Pure MVC Architecture with Security Hardening
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

// Generate unique correlation / Request ID
$requestId = bin2hex(random_bytes(8));
if (!headers_sent()) {
    header("X-Request-Id: {$requestId}");

    // Security Headers (1.13)
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https: blob:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none';");

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    if ($isHttps) {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
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

// Require global helper functions (includes mb_strlen and mb_substr polyfills)
require ROOT_PATH . '/app/Core/helpers.php';

// Start secure session
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
    error_log("[Zebra Tours Error #{$requestId}]: " . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    http_response_code(500);

    $isDebug = getenv('ZT_DEBUG') === '1';

    echo '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8">';
    echo '<title>500 Erro de Servidor | Zebra Tours</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<script src="https://cdn.tailwindcss.com"></script></head>';
    echo '<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6">';
    echo '<div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center space-y-5 shadow-2xl">';
    echo '<div class="w-14 h-14 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl flex items-center justify-center mx-auto text-xl font-bold font-mono">500</div>';
    echo '<h1 class="text-xl font-bold text-white tracking-tight">Ocorreu um erro no servidor</h1>';

    if ($isDebug) {
        echo '<div class="text-left bg-black/60 p-4 rounded-xl border border-red-900/50 font-mono text-xs text-red-300 overflow-x-auto space-y-2">';
        echo '<div><strong>Erro:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div><strong>Ficheiro:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . ':' . $e->getLine() . '</div>';
        echo '<div><strong>ID:</strong> ' . htmlspecialchars($requestId, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '</div>';
    } else {
        echo '<p class="text-xs text-slate-400 leading-relaxed">Não foi possível processar o seu pedido neste momento. Por favor tente novamente dentro de instantes.</p>';
        echo '<p class="text-[10px] text-slate-600 font-mono">Código de Referência: ' . htmlspecialchars($requestId, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    echo '<a href="/" class="inline-block px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider transition">Voltar à Página Inicial</a>';
    echo '</div></body></html>';
}
