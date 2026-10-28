<?php
/**
 * Global View & Utility Helpers for Zebra Tours PHP MVC
 * Pure native PHP 8.2 with robust polyfills
 */

declare(strict_types=1);

// Polyfill for mb_strlen when mbstring extension is not installed
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $string, ?string $encoding = null): int {
        if ($string === '') {
            return 0;
        }
        // Count UTF-8 unicode code points using PCRE UTF-8 modifier 'u'
        $count = @preg_match_all('/./su', $string);
        if ($count !== false) {
            return $count;
        }
        // Fallback for non-UTF8 strings
        return strlen($string);
    }
}

// Polyfill for mb_substr when mbstring extension is not installed
if (!function_exists('mb_substr')) {
    function mb_substr(string $string, int $start, ?int $length = null, ?string $encoding = null): string {
        if ($string === '') {
            return '';
        }
        $matches = [];
        if (@preg_match_all('/./su', $string, $matches)) {
            $chars = $matches[0];
            $total = count($chars);
            if ($start < 0) {
                $start = max(0, $total + $start);
            }
            if ($start >= $total) {
                return '';
            }
            if ($length === null) {
                return implode('', array_slice($chars, $start));
            }
            if ($length < 0) {
                $length = max(0, $total + $length - $start);
            }
            return implode('', array_slice($chars, $start, $length));
        }
        // Fallback for non-UTF8 strings
        return $length === null ? substr($string, $start) : substr($string, $start, $length);
    }
}

// Helper to safely check digits without depending on ctype_digit
if (!function_exists('is_digits')) {
    function is_digits(mixed $val): bool {
        if (!is_string($val) && !is_int($val)) {
            return false;
        }
        return (bool) preg_match('/^\d+$/', (string) $val);
    }
}

// HTML Escaping
if (!function_exists('e')) {
    function e(mixed $value): string {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// Currency formatting in Meticais (MT)
if (!function_exists('money')) {
    function money(mixed $value, string $currency = 'MT'): string {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return 'Sob Consulta';
        }
        return number_format((float) $value, 0, ',', '.') . ' ' . $currency;
    }
}

// CSRF input field generator
if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        $token = \App\Core\Session::getCsrfToken();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

// Honeypot field for anti-spam
if (!function_exists('honeypot_field')) {
    function honeypot_field(): string {
        return '<div class="hidden" style="display:none !important;" aria-hidden="true">'
            . '<label for="zt_website_hp">Não preencher se for humano</label>'
            . '<input type="text" id="zt_website_hp" name="website" tabindex="-1" autocomplete="off" value="">'
            . '</div>';
    }
}
