<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        // Inject global settings into every view
        $db = Database::getConnection();
        $settingsRows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $navItems = $db->query("SELECT * FROM navigation_items WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

        $data['siteSettings'] = $settings;
        $data['navItems'] = $navItems;
        $data['currentUser'] = Auth::user();

        $html = View::render($view, $data, $layout);
        return Response::html($html);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function validateCsrf(Request $request): bool
    {
        $token = $request->post('_csrf');
        return Session::validateCsrfToken($token);
    }
}
