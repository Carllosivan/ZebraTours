<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class PageController extends Controller
{
    public function show(Request $request, array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'publicado'");
        $stmt->execute([$slug]);
        $page = $stmt->fetch();

        if (!$page) {
            return $this->render('errors/404', [
                'pageTitle' => 'Página Não Encontrada',
                'path'      => "/pagina/{$slug}",
            ]);
        }

        return $this->render('pages/show', [
            'pageTitle'       => ($page['seo_title'] ?: $page['title']) . ' | Zebra Tours',
            'metaDescription' => $page['seo_description'] ?: $page['excerpt'],
            'page'            => $page,
        ]);
    }

    public function about(Request $request): Response
    {
        return $this->render('pages/about', [
            'pageTitle'       => 'Sobre Nós | Zebra Tours Moçambique',
            'metaDescription' => 'Conheça a história, missão e valores da Zebra Tours Moçambique, a sua parceira de confiança em viagens e turismo.',
        ]);
    }

    public function faq(Request $request): Response
    {
        return $this->render('pages/faq', [
            'pageTitle'       => 'Perguntas Frequentes (FAQ) | Zebra Tours',
            'metaDescription' => 'Respostas às dúvidas mais comuns sobre reservas, pagamentos, vistos de viagem e políticas de cancelamento.',
        ]);
    }

    public function terms(Request $request): Response
    {
        return $this->render('pages/terms', [
            'pageTitle'       => 'Termos e Condições Gerais | Zebra Tours',
            'metaDescription' => 'Termos e condições de prestação de serviços turísticos, cotações e contratos de viagem.',
        ]);
    }

    public function privacy(Request $request): Response
    {
        return $this->render('pages/privacy', [
            'pageTitle'       => 'Política de Privacidade | Zebra Tours',
            'metaDescription' => 'Como tratamos e protegemos os seus dados pessoais e informações de reservas.',
        ]);
    }
}
