<?php
/**
 * Global View Helpers for Zebra Tours PHP MVC
 */

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('money')) {
    function money(mixed $value, string $currency = 'MT'): string {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return 'Sob Consulta';
        }
        return number_format((float) $value, 0, ',', '.') . ' ' . $currency;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        $token = \App\Core\Session::getCsrfToken();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
