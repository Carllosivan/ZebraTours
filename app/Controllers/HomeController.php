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
}
