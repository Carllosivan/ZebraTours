<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Media;
use App\Services\UploadService;
use Throwable;
use InvalidArgumentException;

/**
 * Controller responsável pela gestão e upload de ficheiros na Biblioteca Multimédia.
 * Intermedeia a View do CMS, as validações do UploadService e a persistência no Model Media.
 */
class MediaController extends Controller
{
    private Media $mediaModel;
    private UploadService $uploadService;

    public function __construct(?Media $mediaModel = null, ?UploadService $uploadService = null)
    {
        $this->mediaModel = $mediaModel ?? new Media();
        $this->uploadService = $uploadService ?? new UploadService();
    }

    /**
     * Exibe a listagem dos ficheiros da Biblioteca Multimédia com filtros.
     */
    public function index(Request $request): Response
    {
        $category = $request->get('categoria');
        $page = max(1, (int) $request->get('pagina', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        $items = $this->mediaModel->getAll($category, $perPage, $offset);
        $totalItems = $this->mediaModel->countAll($category);
        $totalPages = (int) ceil($totalItems / $perPage);

        return $this->render('media/index', [
            'items'         => $items,
            'currentCategory' => $category ?? 'todos',
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalItems'    => $totalItems,
            'flashMessage'  => $this->getFlashMessage(),
        ]);
    }

    /**
     * Exibe o formulário de upload de multimédia.
     */
    public function create(Request $request): Response
    {
        return $this->render('media/upload', [
            'errors'  => [],
            'success' => null,
            'old'     => [],
        ]);
    }

    /**
     * Processa a submissão do formulário de upload (POST).
     * Suporta validação individual ou múltipla de ficheiros.
     */
    public function store(Request $request): Response
    {
        $errors = [];
        $uploadedAssets = [];

        $fileData = $request->file('media_file');
        $title = (string) $request->post('title', '');
        $altText = (string) $request->post('alt_text', '');
        $categoryInput = (string) $request->post('category', '');

        // 1. Validação da presença do ficheiro
        if (!$fileData || empty($fileData['tmp_name'])) {
            $errors[] = 'Por favor, selecione um ficheiro multimédia para efetuar o envio.';
            return $this->render('media/upload', [
                'errors'  => $errors,
                'success' => null,
                'old'     => $request->all(),
            ], 422);
        }

        try {
            // 2. Processamento, validação MIME e gravação física segura pelo Serviço
            $uploadResult = $this->uploadService->handle($fileData);

            // 3. Montagem dos metadados para registo na base de dados
            $recordData = [
                'filename'      => $uploadResult['filename'],
                'file_path'     => $uploadResult['file_path'],
                'original_name' => $uploadResult['original_name'],
                'mime_type'     => $uploadResult['mime_type'],
                'file_size'     => $uploadResult['file_size'],
                'title'         => $title !== '' ? $title : pathinfo($uploadResult['original_name'], PATHINFO_FILENAME),
                'alt_text'      => $altText !== '' ? $altText : $title,
                'category'      => $categoryInput !== '' ? $categoryInput : $uploadResult['category'],
            ];

            // 4. Sanitização e inserção na base de dados pelo Model
            $newId = $this->mediaModel->create($recordData);
            $recordData['id'] = $newId;
            $uploadedAssets[] = $recordData;

            // Se a requisição esperar JSON (ex: AJAX/Dropzone)
            if ($this->wantsJson($request)) {
                return $this->json([
                    'success' => true,
                    'message' => 'Ficheiro enviado e registado com sucesso na Biblioteca Multimédia!',
                    'data'    => $recordData,
                ], 201);
            }

            // Redirecionamento com mensagem de sucesso na View
            $this->setFlashMessage('success', "Ficheiro '{$uploadResult['original_name']}' carregado e adicionado à biblioteca com sucesso!");
            return $this->render('media/upload', [
                'errors'  => [],
                'success' => "Ficheiro '{$uploadResult['original_name']}' processado e guardado com sucesso!",
                'asset'   => $recordData,
                'old'     => [],
            ], 200);

        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Throwable $e) {
            // Log do erro técnico internamente sem expor detalhes sensíveis
            error_log('[MediaController Upload Error]: ' . $e->getMessage());
            $errors[] = 'Ocorreu uma falha inesperada durante o processamento do ficheiro. Verifique o tamanho e o formato do arquivo e tente novamente.';
        }

        // Resposta em caso de erro
        if ($this->wantsJson($request)) {
            return $this->json([
                'success' => false,
                'errors'  => $errors,
            ], 422);
        }

        return $this->render('media/upload', [
            'errors'  => $errors,
            'success' => null,
            'old'     => $request->all(),
        ], 422);
    }

    /**
     * Remove com segurança o ficheiro físico do disco e o registo correspondente na base de dados.
     */
    public function delete(Request $request, array $params = []): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id', 0));

        $media = $this->mediaModel->findById($id);
        if (!$media) {
            if ($this->wantsJson($request)) {
                return $this->json(['success' => false, 'message' => 'Ficheiro multimédia não encontrado.'], 404);
            }
            return $this->redirect('/media?erro=nao_encontrado');
        }

        // 1. Eliminação física com proteção contra path traversal
        $publicDir = defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 2) . '/public';
        $safeFilename = basename((string) $media['filename']);
        $filePath = $publicDir . '/uploads/multimedia/' . $safeFilename;

        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        // 2. Eliminação do registo na base de dados
        $this->mediaModel->delete($id);

        if ($this->wantsJson($request)) {
            return $this->json(['success' => true, 'message' => 'Ficheiro removido com sucesso.']);
        }

        $this->setFlashMessage('success', 'Ficheiro multimédia removido com sucesso.');
        return $this->redirect('/media');
    }

    /**
     * Verifica se a requisição solicita retorno JSON.
     */
    private function wantsJson(Request $request): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xReq = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($xReq) === 'xmlhttprequest';
    }

    private function setFlashMessage(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function getFlashMessage(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (isset($_SESSION['flash'])) {
            $msg = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $msg;
        }
        return null;
    }
}
