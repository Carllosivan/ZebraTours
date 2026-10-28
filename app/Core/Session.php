<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    private const INACTIVITY_TIMEOUT = 7200;  // 2 hours
    private const ABSOLUTE_TIMEOUT   = 43200; // 12 hours

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');

            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }

        $now = time();

        // 1. Check inactivity timeout (2 hours)
        if (isset($_SESSION['_last_activity']) && ($now - (int) $_SESSION['_last_activity'] > self::INACTIVITY_TIMEOUT)) {
            self::destroy();
            session_start();
            self::setFlash('error', 'A sua sessão expirou por inatividade (limite de 2 horas). Por favor autentique-se novamente.');
        }

        // 2. Check absolute timeout (12 hours)
        if (isset($_SESSION['_created_at']) && ($now - (int) $_SESSION['_created_at'] > self::ABSOLUTE_TIMEOUT)) {
            self::destroy();
            session_start();
            self::setFlash('error', 'A sua sessão atingiu o tempo máximo de validade (12 horas). Por favor autentique-se novamente.');
        }

        if (!isset($_SESSION['_created_at'])) {
            $_SESSION['_created_at'] = $now;
        }
        $_SESSION['_last_activity'] = $now;

        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        session_regenerate_id($deleteOldSession);
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['_created_at'] = time();
        $_SESSION['_last_activity'] = time();
    }

    public static function getCsrfToken(): string
    {
        self::start();
        return $_SESSION['_csrf_token'] ?? '';
    }

    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        if ($token === null || $token === '') {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        self::start();
        if (isset($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
