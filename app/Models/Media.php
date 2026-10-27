<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Media
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $category = null, int $limit = 48, int $offset = 0): array
    {
        $sql = "SELECT * FROM media_library WHERE 1=1";
        $params = [];
        if ($category && $category !== 'todos' && $category !== 'all') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        
        $i = 1;
        foreach ($params as $p) {
            $stmt->bindValue($i++, $p);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll(?string $category = null): int
    {
        $sql = "SELECT count(*) FROM media_library WHERE 1=1";
        $params = [];
        if ($category && $category !== 'todos' && $category !== 'all') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM media_library WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO media_library (filename, file_path, original_name, mime_type, file_size, title, alt_text, category)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['filename'],
            $data['file_path'],
            $data['original_name'],
            $data['mime_type'] ?? 'image/jpeg',
            $data['file_size'] ?? 0,
            $data['title'] ?? '',
            $data['alt_text'] ?? '',
            $data['category'] ?? 'geral',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM media_library WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
