<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class ContactMessage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, status)
            VALUES (?, ?, ?, ?, ?, 'Pendente')
        ");
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            !empty($data['phone']) ? trim($data['phone']) : null,
            trim($data['subject']),
            trim($data['message']),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
    }
}
