<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $basePath = dirname(__DIR__) . '/Views/';
        $viewFile = $basePath . $template . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("Ficheiro de vista não encontrado: {$template}");
        }

        extract($data);

        // Helper functions for views
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
                $token = Session::getCsrfToken();
                return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
            }
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = $basePath . $layout . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                include $layoutFile;
                return ob_get_clean();
            }
        }

        return $content;
    }
}
