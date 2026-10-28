<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main', int $status = 200): Response
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
        return Response::html($html, $status);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function notFound(string $path = ''): Response
    {
        return $this->render('errors/404', [
            'pageTitle' => 'Página Não Encontrada | Zebra Tours',
            'path'      => $path,
        ], 'layouts/main', 404);
    }

    protected function validateCsrf(Request $request): bool
    {
        $token = $request->post('_csrf') ?: $request->header('X-CSRF-Token');
        return Session::validateCsrfToken($token ? (string) $token : null);
    }
}
