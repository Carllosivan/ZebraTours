<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Package;
use App\Models\Destination;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $packageModel = new Package();
        $destinationModel = new Destination();
        $db = Database::getConnection();

        $featuredPackages = $packageModel->findFeatured(6);
        $destinations = $destinationModel->getAll(true);
        $banners = $db->query("SELECT * FROM banners WHERE status = 'ativo' ORDER BY sort_order ASC")->fetchAll();
        $categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

        return $this->render('home/index', [
            'pageTitle'        => 'Zebra Tours Moçambique | Agência Oficial de Viagens e Turismo',
            'metaDescription'  => 'Viagens inesquecíveis, passagens aéreas aos melhores preços, alojamentos exclusivos e assistência completa ao viajante em Moçambique e no mundo.',
            'featuredPackages' => $featuredPackages,
            'destinations'     => $destinations,
            'banners'          => $banners,
            'categories'       => $categories,
        ]);
    }

    public function sitemap(Request $request): Response
    {
        $db = Database::getConnection();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'zebratours.co.mz';
        $baseUrl = "{$protocol}://{$host}";

        $packages = $db->query("SELECT slug, updated_at FROM packages WHERE status IN ('publicado', 'pendente_validacao')")->fetchAll();
        $destinations = $db->query("SELECT slug, updated_at FROM destinations WHERE status = 'publicado'")->fetchAll();
        $pages = $db->query("SELECT slug, updated_at FROM pages WHERE status = 'publicado'")->fetchAll();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $addUrl = function (string $loc, string $lastmod, string $changefreq, string $priority) use (&$xml, $baseUrl) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars("{$baseUrl}{$loc}", ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . substr($lastmod, 0, 10) . "</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        };

        $now = date('Y-m-d');
        $addUrl('/', $now, 'daily', '1.0');
        $addUrl('/destinos', $now, 'weekly', '0.9');
        $addUrl('/pacotes', $now, 'daily', '0.9');
        $addUrl('/servicos', $now, 'monthly', '0.8');
        $addUrl('/cotacao', $now, 'weekly', '0.9');
        $addUrl('/contactos', $now, 'monthly', '0.7');
        $addUrl('/sobre-nos', $now, 'monthly', '0.6');

        foreach ($destinations as $d) {
            $addUrl('/destinos/' . $d['slug'], $d['updated_at'] ?? $now, 'weekly', '0.8');
        }

        foreach ($packages as $p) {
            $addUrl('/pacotes/' . $p['slug'], $p['updated_at'] ?? $now, 'daily', '0.9');
        }

        foreach ($pages as $pg) {
            $addUrl('/pagina/' . $pg['slug'], $pg['updated_at'] ?? $now, 'monthly', '0.5');
        }

        $xml .= '</urlset>';

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots(Request $request): Response
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'zebratours.co.mz';
        $baseUrl = "{$protocol}://{$host}";

        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Allow: /\n\n";
        $content .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        return new Response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
