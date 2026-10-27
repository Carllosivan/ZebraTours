<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Package;
use App\Models\Destination;

class PackageController extends Controller
{
    public function index(Request $request): Response
    {
        $packageModel = new Package();
        $destinationModel = new Destination();
        $db = Database::getConnection();

        $filters = [
            'destination' => $request->get('destino'),
            'category'    => $request->get('categoria'),
            'search'      => $request->get('pesquisa'),
            'min_price'   => $request->get('min_preco'),
            'max_price'   => $request->get('max_preco'),
            'sort'        => $request->get('ordenar', 'recommended'),
        ];

        $packages = $packageModel->getAll($filters);
        $destinations = $destinationModel->getAll(true);
        $categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

        return $this->render('packages/index', [
            'pageTitle'    => 'Pacotes Turísticos Exclusivos | Zebra Tours Moçambique',
            'metaDescription' => 'Explore a nossa seleção de pacotes turísticos completos com voos, transferes, alojamento de luxo e passeios guiados.',
            'packages'     => $packages,
            'destinations' => $destinations,
            'categories'   => $categories,
            'filters'      => $filters,
        ]);
    }

    public function show(Request $request, array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $packageModel = new Package();
        $package = $packageModel->findBySlug($slug);

        if (!$package) {
            return $this->render('errors/404', [
                'pageTitle' => 'Pacote Não Encontrado',
                'path'      => "/pacotes/{$slug}",
            ]);
        }

        // Related packages
        $related = $packageModel->findFeatured(3);

        return $this->render('packages/show', [
            'pageTitle'       => "{$package['title']} | Zebra Tours Moçambique",
            'metaDescription' => $package['summary'],
            'package'         => $package,
            'related'         => $related,
        ]);
    }
}
