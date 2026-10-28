<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Destination;

class DestinationController extends Controller
{
    public function index(Request $request): Response
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getAll(true);

        return $this->render('destinations/index', [
            'pageTitle'    => 'Destinos de Sonho em Moçambique e no Mundo | Zebra Tours',
            'metaDescription' => 'Descubra os destinos mais procurados: Ilhas Bazaruto, Vilankulos, Ponta do Ouro, Parque Nacional da Gorongosa e muito mais.',
            'destinations' => $destinations,
        ]);
    }

    public function show(Request $request, array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $destinationModel = new Destination();
        $destination = $destinationModel->findBySlug($slug);

        if (!$destination) {
            return $this->notFound("/destinos/{$slug}");
        }

        return $this->render('destinations/show', [
            'pageTitle'       => "{$destination['name']} | Zebra Tours Moçambique",
            'metaDescription' => $destination['summary'],
            'destination'     => $destination,
        ]);
    }
}
