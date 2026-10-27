<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Booking
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public static function generateReferenceCode(): string
    {
        $year = date('Y');
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $rand = '';
        for ($i = 0; $i < 4; $i++) {
            $rand .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return "ZT-{$year}-{$rand}";
    }

    public function create(array $data): string
    {
        $ref = self::generateReferenceCode();
        $stmt = $this->db->prepare("
            INSERT INTO booking_requests (
                reference_code, package_id, customer_name, customer_email,
                customer_phone, phone_country_code, service_type, destination,
                preferred_dates, travelers_count, message, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')
        ");
        $stmt->execute([
            $ref,
            !empty($data['package_id']) ? (int) $data['package_id'] : null,
            trim($data['customer_name']),
            !empty($data['customer_email']) ? trim($data['customer_email']) : null,
            trim($data['customer_phone']),
            $data['phone_country_code'] ?? '+258',
            trim($data['service_type']),
            !empty($data['destination']) ? trim($data['destination']) : null,
            !empty($data['preferred_dates']) ? trim($data['preferred_dates']) : null,
            max(1, (int) ($data['travelers_count'] ?? 1)),
            !empty($data['message']) ? trim($data['message']) : null,
        ]);

        return $ref;
    }

    public function getAll(?string $status = null, ?string $search = null): array
    {
        $sql = "
            SELECT b.*, p.title as package_title, p.price_per_person, p.currency
            FROM booking_requests b
            LEFT JOIN packages p ON p.id = b.package_id
            WHERE 1=1
        ";
        $params = [];
        if ($status && $status !== 'all') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (b.reference_code LIKE ? OR b.customer_name LIKE ? OR b.customer_phone LIKE ?)";
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, p.title as package_title, p.price_per_person, p.currency
            FROM booking_requests b
            LEFT JOIN packages p ON p.id = b.package_id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();
        if (!$booking) return null;

        $histStmt = $this->db->prepare("SELECT * FROM booking_history WHERE booking_id = ? ORDER BY created_at DESC");
        $histStmt->execute([$id]);
        $booking['history'] = $histStmt->fetchAll();

        return $booking;
    }

    public function updateStatus(int $id, string $status, ?string $adminNotes = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE booking_requests 
            SET status = ?, admin_notes = COALESCE(?, admin_notes), updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$status, $adminNotes, $id]);
    }
}
