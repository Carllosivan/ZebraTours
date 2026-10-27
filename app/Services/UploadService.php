<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use InvalidArgumentException;

/**
 * Serviço especializado em processamento, validação e armazenamento seguro de uploads multimédia.
 * Segue os princípios de Clean Code, Single Responsibility (SRP) e Defesa em Profundidade.
 */
class UploadService
{
    /**
     * Tamanho máximo global de upload permitido (50 MB em bytes).
     */
    public const MAX_FILE_SIZE = 50 * 1024 * 1024; // 52.428.800 bytes

    /**
     * Mapeamento rigoroso de extensões permitidas, MIME types oficiais e categorias.
     * Não confia no tipo informado pelo navegador do cliente.
     */
    private const ALLOWED_TYPES = [
        // Imagens
        'jpg'  => ['mime' => 'image/jpeg', 'category' => 'imagem'],
        'jpeg' => ['mime' => 'image/jpeg', 'category' => 'imagem'],
        'png'  => ['mime' => 'image/png',  'category' => 'imagem'],
        'webp' => ['mime' => 'image/webp', 'category' => 'imagem'],

        // Vídeos
        'mp4'  => ['mime' => 'video/mp4',  'category' => 'video'],

        // Áudios
        'mp3'  => ['mime' => 'audio/mpeg', 'category' => 'audio'],

        // Documentos
        'pdf'  => ['mime' => 'application/pdf', 'category' => 'documento'],
    ];

    private string $uploadDir;
    private string $publicUrlPrefix;

    /**
     * @param string|null $uploadDir Diretório físico de destino no servidor
     * @param string $publicUrlPrefix Prefixo público para acesso via URL
     */
    public function __construct(?string $uploadDir = null, string $publicUrlPrefix = '/uploads/multimedia/')
    {
        $this->uploadDir = $uploadDir ?? (defined('PUBLIC_PATH') ? PUBLIC_PATH . '/uploads/multimedia' : dirname(__DIR__, 2) . '/public/uploads/multimedia');
        $this->publicUrlPrefix = '/' . trim($publicUrlPrefix, '/') . '/';

        $this->ensureUploadDirectoryExists();
    }

    /**
     * Executa o tratamento completo de upload com validações de segurança.
     *
     * @param array $file Entrada do array $_FILES['chave']
     * @return array Metadados sanitizados do arquivo processado
     * @throws InvalidArgumentException Se a validação falhar
     * @throws RuntimeException Se ocorrer erro ao mover o ficheiro
     */
    public function handle(array $file): array
    {
        // 1. Validação do código de erro nativo do PHP
        $this->validatePhpUploadError((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE));

        // 2. Validação se o arquivo foi realmente enviado via HTTP POST
        $tmpName = $file['tmp_name'] ?? '';
        if (!is_string($tmpName) || !is_uploaded_file($tmpName)) {
            throw new InvalidArgumentException('Tentativa de upload inválida ou o arquivo temporário não foi reconhecido.');
        }

        // 3. Validação do tamanho do ficheiro
        $fileSize = (int) ($file['size'] ?? 0);
        if ($fileSize <= 0 || $fileSize > self::MAX_FILE_SIZE) {
            $maxMb = self::MAX_FILE_SIZE / (1024 * 1024);
            throw new InvalidArgumentException("O ficheiro excede o tamanho máximo permitido de {$maxMb} MB.");
        }

        // 4. Sanitização e extração da extensão a partir do nome original
        $originalName = (string) ($file['name'] ?? 'arquivo');
        $sanitizedOriginalName = $this->sanitizeOriginalFilename($originalName);
        $extension = strtolower(pathinfo($sanitizedOriginalName, PATHINFO_EXTENSION));

        if (!array_key_exists($extension, self::ALLOWED_TYPES)) {
            $allowedList = implode(', ', array_unique(array_keys(self::ALLOWED_TYPES)));
            throw new InvalidArgumentException("Extensão de ficheiro '.{$extension}' não é permitida. Tipos suportados: {$allowedList}.");
        }

        // 5. Validação rigorosa do MIME type real no servidor (usando libmagic/fileinfo)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new RuntimeException('Falha ao inicializar a biblioteca Fileinfo do PHP.');
        }

        $detectedMime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $expectedMime = self::ALLOWED_TYPES[$extension]['mime'];
        $category = self::ALLOWED_TYPES[$extension]['category'];

        // Alguns servidores identificam mp3 como audio/mp3 ou audio/x-mp3, ou mp4 como video/quicktime
        if (!$this->isMimeCompatible($detectedMime, $expectedMime, $extension)) {
            throw new InvalidArgumentException(
                "O conteúdo real do ficheiro ({$detectedMime}) não corresponde à extensão fornecida (.{$extension})."
            );
        }

        // 6. Geração de nome único e seguro (hash criptográfico aleatório + timestamp + extensão segura)
        $uniqueFilename = sprintf(
            '%s_%s.%s',
            bin2hex(random_bytes(16)),
            time(),
            $extension
        );

        $targetPath = $this->uploadDir . '/' . $uniqueFilename;

        // 7. Mover o ficheiro com permissões controladas (0644 - leitura para web, sem execução)
        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Erro interno ao gravar o ficheiro no diretório de destino.');
        }

        @chmod($targetPath, 0644);

        // 8. Retorno estruturado com metadados para persistência no Model
        return [
            'filename'      => $uniqueFilename,
            'file_path'     => $this->publicUrlPrefix . $uniqueFilename,
            'original_name' => $sanitizedOriginalName,
            'mime_type'     => $detectedMime,
            'file_size'     => $fileSize,
            'category'      => $category,
            'extension'     => $extension,
        ];
    }

    /**
     * Sanitiza o nome original do ficheiro para evitar path traversal ou caracteres especiais perigosos.
     */
    private function sanitizeOriginalFilename(string $filename): string
    {
        // Remove possíveis caminhos relativos ou barras
        $basename = basename($filename);
        // Remove caracteres de controlo e bytes nulos
        $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $basename);
        // Substitui espaços por underscores e mantém caracteres legíveis seguros
        $clean = preg_replace('/[^\p{L}\p{N}\._\-]/u', '_', $clean);
        return trim($clean, '._-');
    }

    /**
     * Garante a existência do diretório de uploads e proteção com .htaccess
     */
    private function ensureUploadDirectoryExists(): void
    {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true) && !is_dir($this->uploadDir)) {
                throw new RuntimeException("Não foi possível criar o diretório de uploads: {$this->uploadDir}");
            }
        }

        // Cria ficheiro .htaccess se não existir para prevenir execução de scripts
        $htaccessPath = $this->uploadDir . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = <<<HTACCESS
# Proteção contra execução de scripts maliciosos na pasta de uploads
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
<IfModule mod_php7.c>
    php_flag engine off
</IfModule>
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>

<FilesMatch "\.(php|phtml|php3|php4|php5|php7|php8|phps|phar|sh|pl|cgi|py|exe)$">
    Order Deny,Allow
    Deny from all
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>

Options -Indexes -ExecCGI
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
</IfModule>
HTACCESS;
            @file_put_contents($htaccessPath, $htaccessContent);
        }
    }

    /**
     * Valida variações aceitáveis de MIME types para codecs de áudio/vídeo.
     */
    private function isMimeCompatible(string $detectedMime, string $expectedMime, string $extension): bool
    {
        if ($detectedMime === $expectedMime) {
            return true;
        }

        // Variações comuns de MP3
        if ($extension === 'mp3' && in_array($detectedMime, ['audio/mpeg', 'audio/mp3', 'audio/x-mp3'], true)) {
            return true;
        }

        // Variações comuns de MP4
        if ($extension === 'mp4' && in_array($detectedMime, ['video/mp4', 'video/quicktime'], true)) {
            return true;
        }

        return false;
    }

    /**
     * Mapeia os códigos de erro padrão da diretiva de upload do PHP.
     */
    private function validatePhpUploadError(int $errorCode): void
    {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new InvalidArgumentException('O ficheiro ultrapassou o limite máximo de tamanho permitido pelo servidor.');
            case UPLOAD_ERR_PARTIAL:
                throw new InvalidArgumentException('O upload foi interrompido e o ficheiro foi recebido apenas parcialmente.');
            case UPLOAD_ERR_NO_FILE:
                throw new InvalidArgumentException('Nenhum ficheiro foi selecionado para upload.');
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new RuntimeException('Diretório temporário do servidor não configurado.');
            case UPLOAD_ERR_CANT_WRITE:
                throw new RuntimeException('Falha ao gravar o ficheiro no disco do servidor.');
            case UPLOAD_ERR_EXTENSION:
                throw new RuntimeException('Uma extensão do PHP interrompeu o upload do ficheiro.');
            default:
                throw new RuntimeException('Erro desconhecido durante o upload do ficheiro.');
        }
    }
}
