<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $db;

    // Fictitious constant-time bcrypt hash for non-existent emails
    private const DUMMY_HASH = '$2y$10$cf.zToursMozSecurityDummyHashForTimingAttackPrevention22';

    // Known weak/default passwords blocked by defense-in-depth
    private const KNOWN_WEAK_PASSWORDS = [
        'password',
        'admin123',
        'ZebraAdmin2026!',
        'admin',
        '12345678',
        '123456',
        'secret',
        'admin@123',
    ];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function authenticate(string $email, string $password): ?array
    {
        $cleanEmail = strtolower(trim($email));

        // Defense-in-depth: Immediately block known weak/default passwords at login
        if (in_array($password, self::KNOWN_WEAK_PASSWORDS, true)) {
            // Still perform dummy hash verify for constant-time
            password_verify($password, self::DUMMY_HASH);
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND status = 'ativo'");
        $stmt->execute([$cleanEmail]);
        $user = $stmt->fetch();

        if (!$user) {
            // Constant-time execution prevents user enumeration via timing
            password_verify($password, self::DUMMY_HASH);
            return null;
        }

        $storedHash = (string) ($user['password_hash'] ?? '');

        // Reject invalid hashes ('!', empty, or corrupted)
        if ($storedHash === '!' || empty($storedHash) || !str_starts_with($storedHash, '$')) {
            password_verify($password, self::DUMMY_HASH);
            return null;
        }

        if (!password_verify($password, $storedHash)) {
            return null;
        }

        // Rehash password if algorithm or cost changed
        if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $rehashStmt = $this->db->prepare("UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $rehashStmt->execute([$newHash, $user['id']]);
            $user['password_hash'] = $newHash;
        }

        return $user;
    }

    public function getAll(): array
    {
        return $this->db->query("
            SELECT id, name, email, role, status, created_at, updated_at
            FROM users
            ORDER BY name ASC
        ")->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, status, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, status, created_at, updated_at FROM users WHERE email = ?");
        $stmt->execute([strtolower(trim($email))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($data['name']),
            strtolower(trim($data['email'])),
            $data['password_hash'],
            $data['role'] ?? 'editor',
            $data['status'] ?? 'ativo',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $params[] = trim($data['name']);
        }
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = strtolower(trim($data['email']));
        }
        if (isset($data['role'])) {
            $fields[] = 'role = ?';
            $params[] = $data['role'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $params[] = $data['status'];
        }

        if (empty($fields)) {
            return in_array($password, self::KNOWN_WEAK_PASSWORDS, true);
        }

        $fields[] = 'updated_at = CURRENT_TIMESTAMP';
        $params[] = $id;

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ?, status = 'ativo', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }
}
