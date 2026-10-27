<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use InvalidArgumentException;

/**
 * Model responsável pela persistência, sanitização e consulta de metadados multimédia.
 * Mapeia os registos na tabela 'media_library' utilizando PDO com prepared statements.
 */
class Media
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->ensureTableExists();
    }

    /**
     * Sanitiza os dados e insere um novo registo multimédia na base de dados.
     *
     * @param array{
     *   filename: string,
     *   file_path: string,
     *   original_name: string,
     *   mime_type: string,
     *   file_size: int,
     *   title?: ?string,
     *   alt_text?: ?string,
     *   category?: ?string
     * } $data
     * @return int ID do registo inserido
     */
    public function create(array $data): int
    {
        // 1. Sanitização rigorosa dos campos de entrada
        $filename      = trim(strip_tags($data['filename'] ?? ''));
        $filePath      = trim(strip_tags($data['file_path'] ?? ''));
        $originalName  = htmlspecialchars(trim(strip_tags($data['original_name'] ?? '')), ENT_QUOTES, 'UTF-8');
        $mimeType      = trim(strip_tags($data['mime_type'] ?? ''));
        $fileSize      = (int) ($data['file_size'] ?? 0);
        $title         = isset($data['title']) && $data['title'] !== '' 
                         ? htmlspecialchars(trim(strip_tags($data['title'])), ENT_QUOTES, 'UTF-8') 
                         : null;
        $altText       = isset($data['alt_text']) && $data['alt_text'] !== '' 
                         ? htmlspecialchars(trim(strip_tags($data['alt_text'])), ENT_QUOTES, 'UTF-8') 
                         : null;
        $category      = $this->sanitizeCategory($data['category'] ?? 'geral');

        // Validação dos campos obrigatórios
        if ($filename === '' || $filePath === '' || $originalName === '' || $mimeType === '' || $fileSize <= 0) {
            throw new InvalidArgumentException('Dados obrigatórios incompletos para registo multimédia no Model.');
        }

        // 2. Inserção segura com Prepared Statements
        $sql = "INSERT INTO media_library (
                    filename, file_path, original_name, mime_type, file_size, title, alt_text, category, created_at
                ) VALUES (
                    :filename, :file_path, :original_name, :mime_type, :file_size, :title, :alt_text, :category, datetime('now')
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':filename'      => $filename,
            ':file_path'     => $filePath,
            ':original_name' => $originalName,
            ':mime_type'     => $mimeType,
            ':file_size'     => $fileSize,
            ':title'         => $title,
            ':alt_text'      => $altText,
            ':category'      => $category,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Procura um ficheiro multimédia pelo seu ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM media_library WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Lista ficheiros com suporte a filtro opcional por categoria e paginação.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(?string $category = null, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);

        if ($category !== null && $category !== '' && $category !== 'todos') {
            $sql = "SELECT * FROM media_library 
                    WHERE category = :category 
                    ORDER BY id DESC 
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        } else {
            $sql = "SELECT * FROM media_library 
                    ORDER BY id DESC 
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Conta o total de ficheiros multimédia registados.
     */
    public function countAll(?string $category = null): int
    {
        if ($category !== null && $category !== '' && $category !== 'todos') {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM media_library WHERE category = :category");
            $stmt->execute([':category' => $category]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM media_library");
        }

        return (int) $stmt->fetchColumn();
    }

    /**
     * Remove o registo da base de dados.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM media_library WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Valida e normaliza categorias aceites.
     */
    private function sanitizeCategory(?string $category): string
    {
        $allowed = ['imagem', 'video', 'audio', 'documento', 'destinos', 'pacotes', 'hero', 'geral'];
        $clean = strtolower(trim((string) $category));
        return in_array($clean, $allowed, true) ? $clean : 'geral';
    }

    /**
     * Garante que a tabela media_library existe na base de dados SQLite.
     */
    private function ensureTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS media_library (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filename TEXT NOT NULL,
            file_path TEXT NOT NULL,
            original_name TEXT NOT NULL,
            mime_type TEXT NOT NULL,
            file_size INTEGER NOT NULL,
            title TEXT,
            alt_text TEXT,
            category TEXT DEFAULT 'geral',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }
}
