<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Media;

class MediaController extends Controller
{
    private Media $mediaModel;

    public function __construct()
    {
        $this->mediaModel = new Media();
    }

    public function index(Request $request): Response
    {
        $category = $request->get('categoria', 'todos');
        $page = max(1, (int) $request->get('pagina', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        $items = $this->mediaModel->getAll($category, $perPage, $offset);
        $totalItems = $this->mediaModel->countAll($category);
        $totalPages = (int) ceil($totalItems / $perPage);

        return $this->render('admin/media/index', [
            'pageTitle'       => 'Biblioteca Multimédia | Zebra Tours CMS',
            'items'           => $items,
            'currentCategory' => $category,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'totalItems'      => $totalItems,
            'successMessage'  => Session::getFlash('success'),
            'errorMessage'    => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/media/upload', [
            'pageTitle' => 'Enviar Novo Ficheiro Multimédia | Zebra Tours CMS',
            'errors'    => [],
            'old'       => [],
        ], 'layouts/admin');
    }

    public function store(Request $request): Response
    {
        $url = trim((string) $request->post('url', ''));
        $title = trim((string) $request->post('title', ''));
        $altText = trim((string) $request->post('alt_text', ''));
        $category = trim((string) $request->post('category', 'geral'));

        $fileData = $request->file('media_file');

        $filePath = '';
        $filename = '';

        if ($fileData && !empty($fileData['tmp_name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/multimedia';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
            $safeName = 'zt_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = $uploadDir . '/' . $safeName;

            if (move_uploaded_file($fileData['tmp_name'], $destination)) {
                $filePath = '/uploads/multimedia/' . $safeName;
                $filename = $safeName;
            }
        } elseif (!empty($url)) {
            $filePath = $url;
            $filename = basename(parse_url($url, PHP_URL_PATH) ?: 'image.jpg');
        }

        if (empty($filePath)) {
            Session::setFlash('error', 'Selecione um ficheiro ou indique um URL de imagem válido.');
            return $this->redirect('/admin/media/upload');
        }

        $record = [
            'filename'      => $filename,
            'file_path'     => $filePath,
            'original_name' => $title ?: $filename,
            'mime_type'     => 'image/jpeg',
            'file_size'     => 150000,
            'title'         => $title ?: pathinfo($filename, PATHINFO_FILENAME),
            'alt_text'      => $altText ?: $title,
            'category'      => $category ?: 'geral',
        ];

        $this->mediaModel->create($record);
        Session::setFlash('success', 'Ficheiro adicionado com sucesso à biblioteca!');
        return $this->redirect('/admin/media');
    }

    public function delete(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id', 0));
        $this->mediaModel->delete($id);
        Session::setFlash('success', 'Ficheiro eliminado da biblioteca.');
        return $this->redirect('/admin/media');
    }
}
