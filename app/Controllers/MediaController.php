<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Media;
use App\Services\AuditService;

class MediaController extends Controller
{
    private Media $mediaModel;

    public function __construct()
    {
        $this->mediaModel = new Media();
    }

    public function index(Request $request): Response
    {
        $category = $request->getString('categoria', 'todos');
        $page = max(1, $request->getInt('pagina', 1));
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
        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : null;

        $url = $request->postString('url');
        $title = $request->postString('title');
        $altText = $request->postString('alt_text');
        $category = $request->postString('category', 'geral');

        $fileData = $request->file('media_file');

        $filePath = '';
        $filename = '';
        $mimeType = 'image/jpeg';
        $fileSize = 0;

        if ($fileData && !empty($fileData['tmp_name']) && is_uploaded_file($fileData['tmp_name'])) {
            $tmpPath = $fileData['tmp_name'];
            $fileSize = (int) ($fileData['size'] ?? filesize($tmpPath));

            // Max 5 MB check (5 * 1024 * 1024)
            if ($fileSize > 5242880) {
                Session::setFlash('error', 'O ficheiro excede o tamanho máximo permitido de 5 MB.');
                return $this->redirect('/admin/media/upload');
            }

            // Detect image properties via getimagesize() inspection (NEVER trust extension or browser mime)
            $imageInfo = @getimagesize($tmpPath);
            if ($imageInfo === false) {
                Session::setFlash('error', 'Ficheiro inválido. Apenas são permitidas imagens reais (JPG, PNG, GIF ou WebP).');
                return $this->redirect('/admin/media/upload');
            }

            [$width, $height, $imageType] = $imageInfo;

            // Enforce max 12000px resolution
            if ($width > 12000 || $height > 12000) {
                Session::setFlash('error', 'A imagem excede a resolução máxima permitida de 12.000 píxeis.');
                return $this->redirect('/admin/media/upload');
            }

            // Derive strictly whitelist-based extension from detected content
            $typeMap = [
                IMAGETYPE_JPEG => ['ext' => 'jpg',  'mime' => 'image/jpeg'],
                IMAGETYPE_PNG  => ['ext' => 'png',  'mime' => 'image/png'],
                IMAGETYPE_GIF  => ['ext' => 'gif',  'mime' => 'image/gif'],
                IMAGETYPE_WEBP => ['ext' => 'webp', 'mime' => 'image/webp'],
            ];

            if (!isset($typeMap[$imageType])) {
                Session::setFlash('error', 'Tipo de imagem não suportado. Formatos aceites: JPG, PNG, GIF e WebP.');
                return $this->redirect('/admin/media/upload');
            }

            $detected = $typeMap[$imageType];
            $ext = $detected['ext'];
            $mimeType = $detected['mime'];

            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/multimedia';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Safe random filename: zt_<timestamp>_<8hex>.<derived_ext>
            $safeName = 'zt_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = $uploadDir . '/' . $safeName;

            if (move_uploaded_file($tmpPath, $destination)) {
                $filePath = '/uploads/multimedia/' . $safeName;
                $filename = $safeName;
                $fileSize = (int) filesize($destination);
            } else {
                Session::setFlash('error', 'Ocorreu um erro ao gravar o ficheiro no servidor.');
                return $this->redirect('/admin/media/upload');
            }
        } elseif (!empty($url)) {
            // Validate URL format and scheme
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Session::setFlash('error', 'O URL indicado não é válido.');
                return $this->redirect('/admin/media/upload');
            }

            $parsed = parse_url($url);
            $scheme = strtolower($parsed['scheme'] ?? '');
            if (!in_array($scheme, ['http', 'https'], true)) {
                Session::setFlash('error', 'Apenas são permitidos URLs com protocolo http ou https.');
                return $this->redirect('/admin/media/upload');
            }

            $filePath = $url;
            $rawPath = parse_url($url, PHP_URL_PATH) ?: '';
            $urlExt = strtolower(pathinfo($rawPath, PATHINFO_EXTENSION));
            if (!in_array($urlExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $urlExt = 'jpg';
            }
            $safeExt = ($urlExt === 'jpeg') ? 'jpg' : $urlExt;
            $filename = 'zt_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
            $mimeType = 'image/' . ($safeExt === 'jpg' ? 'jpeg' : $safeExt);
            $fileSize = 150000;
        }

        if (empty($filePath)) {
            Session::setFlash('error', 'Selecione um ficheiro de imagem válido ou indique um URL.');
            return $this->redirect('/admin/media/upload');
        }

        $record = [
            'filename'      => $filename,
            'file_path'     => $filePath,
            'original_name' => $title ?: ($fileData['name'] ?? $filename),
            'mime_type'     => $mimeType,
            'file_size'     => $fileSize,
            'title'         => $title ?: pathinfo($filename, PATHINFO_FILENAME),
            'alt_text'      => $altText ?: $title,
            'category'      => $category ?: 'geral',
        ];

        $mediaId = $this->mediaModel->create($record);
        AuditService::log('MEDIA_UPLOAD', $userId, 'media_library', $mediaId, "Ficheiro enviado: {$filename} ({$mimeType}, {$fileSize} bytes)");

        Session::setFlash('success', 'Ficheiro adicionado com sucesso à biblioteca!');
        return $this->redirect('/admin/media');
    }

    public function delete(Request $request, array $params): Response
    {
        // Enforce role:admin permission check
        if (!Auth::hasRole(['admin', 'superadmin'])) {
            return Response::html("<h1>403 Acesso Negado</h1><p>Apenas administradores podem eliminar ficheiros multimédia.</p>", 403);
        }

        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : null;

        $id = (int) ($params['id'] ?? $request->post('id', 0));
        $item = $this->mediaModel->findById($id);

        if ($item) {
            $filename = (string) ($item['filename'] ?? '');

            // Delete from disk only if filename strictly matches standard pattern to prevent path traversal
            if (preg_match('/^zt_\d+_[a-f0-9]{8}\.(jpg|png|gif|webp)$/i', $filename)) {
                $fullPath = dirname(__DIR__, 2) . '/public/uploads/multimedia/' . $filename;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $this->mediaModel->delete($id);
            AuditService::log('MEDIA_ELIMINADO', $userId, 'media_library', $id, "Ficheiro eliminado: {$filename}");
            Session::setFlash('success', 'Ficheiro eliminado da biblioteca com sucesso.');
        }

        return $this->redirect('/admin/media');
    }
}
