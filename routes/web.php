<?php
/**
 * Zebra Tours Moçambique - Definição de Rotas Web
 * Arquitetura PHP 8.2+ MVC
 */

declare(strict_types=1);

use App\Controllers\MediaController;
use App\Core\Response;
use App\Core\Database;

/** @var \App\Core\Router $router */

$router->get('/', function() {
    return new Response(
        '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8"><title>Zebra Tours Moçambique</title></head><body>' .
        '<h1>Zebra Tours Moçambique - PHP MVC Backend</h1>' .
        '<p>O seu próximo destino começa aqui!</p>' .
        '<p>Telefone / WhatsApp: +258 85 104 1826 | Maputo, Moçambique</p>' .
        '<p><a href="/media">Aceder à Biblioteca Multimédia (CMS)</a> | <a href="/media/upload">Fazer Upload</a></p>' .
        '</body></html>'
    );
});

// Módulo de Biblioteca Multimédia (MVC)
$router->get('/media', [MediaController::class, 'index']);
$router->get('/media/upload', [MediaController::class, 'create']);
$router->post('/media/upload', [MediaController::class, 'store']);
$router->post('/media/delete', [MediaController::class, 'delete']);

// Endpoints da API e Conteúdos
$router->get('/pacotes', function() {
    $db = Database::getConnection();
    $packages = $db->query("SELECT * FROM packages WHERE status != 'rascunho'")->fetchAll();
    return Response::json($packages);
});

$router->get('/destinos', function() {
    $db = Database::getConnection();
    $destinations = $db->query("SELECT * FROM destinations WHERE status = 'publicado'")->fetchAll();
    return Response::json($destinations);
});

$router->get('/api/settings', function() {
    $db = Database::getConnection();
    $settings = $db->query("SELECT * FROM settings")->fetchAll();
    return Response::json($settings);
});
