<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Controller Base da arquitetura MVC.
 * Fornece métodos utilitários para renderização de views, respostas JSON e redirecionamentos.
 */
abstract class Controller
{
    /**
     * Renderiza uma View PHP passando variáveis de forma segura.
     *
     * @param string $view Caminho relativo da view dentro de /app/Views (ex: 'media/upload')
     * @param array<string, mixed> $data Dados disponibilizados para a view
     * @param int $statusCode Código de estado HTTP (padrão: 200)
     * @return Response
     */
    protected function render(string $view, array $data = [], int $statusCode = 200): Response
    {
        $viewFile = BASE_PATH . '/app/Views/' . trim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Ficheiro de View não encontrado: {$viewFile}");
        }

        // Extrai variáveis no escopo isolado da view
        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        return new Response($content, $statusCode, [
            'Content-Type' => 'text/html; charset=utf-8'
        ]);
    }

    /**
     * Retorna uma resposta formatada em JSON.
     */
    protected function json(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    /**
     * Executa um redirecionamento HTTP seguro.
     */
    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}
