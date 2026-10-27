<?php
declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\PackageController;
use App\Controllers\DestinationController;
use App\Controllers\ServiceController;
use App\Controllers\QuoteController;
use App\Controllers\ContactController;
use App\Controllers\PageController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\MediaController;

/** @var \App\Core\Router $router */

// 1. Public Pages
$router->get('/', [HomeController::class, 'index']);

// 2. Destinations
$router->get('/destinos', [DestinationController::class, 'index']);
$router->get('/destinos/{slug}', [DestinationController::class, 'show']);

// 3. Packages
$router->get('/pacotes', [PackageController::class, 'index']);
$router->get('/pacotes/{slug}', [PackageController::class, 'show']);

// 4. Services
$router->get('/servicos', [ServiceController::class, 'index']);
$router->get('/servicos/{slug}', [ServiceController::class, 'show']);

// 5. Quotation Requests
$router->get('/cotacao', [QuoteController::class, 'create']);
$router->post('/cotacao', [QuoteController::class, 'submit']);
$router->get('/cotacao/sucesso', [QuoteController::class, 'success']);

// 6. Contact & Inquiries
$router->get('/contactos', [ContactController::class, 'index']);
$router->post('/contactos', [ContactController::class, 'submit']);

// 7. Editorial & Corporate Pages
$router->get('/sobre-nos', [PageController::class, 'about']);
$router->get('/faq', [PageController::class, 'faq']);
$router->get('/termos', [PageController::class, 'terms']);
$router->get('/privacidade', [PageController::class, 'privacy']);
$router->get('/pagina/{slug}', [PageController::class, 'show']);

// 8. Authentication (Admin)
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout']);

// 9. Admin Area (Protected by auth middleware)
$router->get('/admin', [AdminController::class, 'dashboard'], ['auth']);
$router->get('/admin/pedidos', [AdminController::class, 'bookings'], ['auth']);
$router->post('/admin/pedidos/{id}/estado', [AdminController::class, 'updateBookingStatus'], ['auth']);
$router->get('/admin/pedidos/exportar-csv', [AdminController::class, 'exportCsv'], ['auth']);
$router->get('/admin/pacotes', [AdminController::class, 'packages'], ['auth']);
$router->get('/admin/destinos', [AdminController::class, 'destinations'], ['auth']);
$router->get('/admin/media', [MediaController::class, 'index'], ['auth']);
$router->get('/admin/media/upload', [MediaController::class, 'create'], ['auth']);
$router->post('/admin/media/upload', [MediaController::class, 'store'], ['auth']);
$router->post('/admin/media/delete', [MediaController::class, 'delete'], ['auth']);
$router->get('/admin/paginas', [AdminController::class, 'pages'], ['auth']);
$router->get('/admin/banners', [AdminController::class, 'banners'], ['auth']);
$router->get('/admin/definicoes', [AdminController::class, 'settings'], ['auth']);
$router->post('/admin/definicoes', [AdminController::class, 'updateSettings'], ['auth']);
