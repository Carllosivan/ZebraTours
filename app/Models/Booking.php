<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
use RuntimeException;

class Booking
{
    private PDO $db;

    public const ALLOWED_STATUSES = [
        'Pendente',
        'Em Análise',
        'Cotado',
        'Confirmado',
        'Cancelado',
    ];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
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
        $packageId = null;
        if (!empty($data['package_id']) && is_numeric($data['package_id'])) {
            $checkStmt = $this->db->prepare("
                SELECT id FROM packages
                WHERE id = ? AND status IN ('publicado', 'pendente_validacao')
            ");
            $checkStmt->execute([(int) $data['package_id']]);
            if ($checkStmt->fetch()) {
                $packageId = (int) $data['package_id'];
            }
        }

        $cleanPhone = preg_replace('/[^\d+]/', '', (string) ($data['customer_phone'] ?? ''));
        $countryCode = trim((string) ($data['phone_country_code'] ?? '+258'));
        $name = trim((string) ($data['customer_name'] ?? ''));
        $email = !empty($data['customer_email']) ? trim((string) $data['customer_email']) : null;
        $service = trim((string) ($data['service_type'] ?? 'Pacote Turístico Completo'));
        $dest = !empty($data['destination']) ? trim((string) $data['destination']) : null;
        $dates = !empty($data['preferred_dates']) ? trim((string) $data['preferred_dates']) : null;
        $travelers = max(1, min(50, (int) ($data['travelers_count'] ?? 1)));
        $message = !empty($data['message']) ? mb_substr(trim((string) $data['message']), 0, 2000) : null;

        $stmt = $this->db->prepare("
            INSERT INTO booking_requests (
                reference_code, package_id, customer_name, customer_email,
                customer_phone, phone_country_code, service_type, destination,
                preferred_dates, travelers_count, message, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')
        ");

        // Retry loop to handle rare UNIQUE reference_code collision
        $maxAttempts = 10;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $ref = self::generateReferenceCode();
            try {
                $stmt->execute([
                    $ref,
                    $packageId,
                    $name,
                    $email,
                    $cleanPhone,
                    $countryCode,
                    $service,
                    $dest,
                    $dates,
                    $travelers,
                    $message,
                ]);

                // Create initial entry in booking_history
                $bookingId = (int) $this->db->lastInsertId();
                $histStmt = $this->db->prepare("
                    INSERT INTO booking_history (booking_id, author_name, action, notes, old_status, new_status)
                    VALUES (?, 'Sistema Online', 'REGISTO_INICIAL', 'Pedido de cotação submetido online pelo cliente', NULL, 'Pendente')
                ");
                $histStmt->execute([$bookingId]);

                return $ref;
            } catch (PDOException $e) {
                // If unique constraint violated on reference_code, retry
                if (str_contains($e->getMessage(), 'UNIQUE') || $e->getCode() === '23000') {
                    continue;
                }
                throw $e;
            }
        }

        throw new RuntimeException("Não foi possível gerar um código de referência único após {$maxAttempts} tentativas.");
    }

    public function updateStatus(int $id, string $status, ?string $notes = null, string $author = 'Administrador'): bool
    {
        $normalizedStatus = null;
        foreach (self::ALLOWED_STATUSES as $s) {
            if (strcasecmp($s, trim($status)) === 0) {
                $normalizedStatus = $s;
                break;
            }
        }

        if (!$normalizedStatus) {
            throw new RuntimeException("Estado inválido fornecido: {$status}");
        }

        $current = $this->findById($id);
        if (!$current) {
            return false;
        }

        $oldStatus = $current['status'];

        $stmt = $this->db->prepare("
            UPDATE booking_requests
            SET status = ?,
                admin_notes = COALESCE(?, admin_notes),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$normalizedStatus, $notes, $id]);

        $histStmt = $this->db->prepare("
            INSERT INTO booking_history (booking_id, author_name, action, notes, old_status, new_status)
            VALUES (?, ?, 'ALTERACAO_ESTADO', ?, ?, ?)
        ");
        $histStmt->execute([$id, $author, $notes, $oldStatus, $normalizedStatus]);

        return true;
    }

    public function addNote(int $id, string $note, string $author = 'Administrador'): bool
    {
        $histStmt = $this->db->prepare("
            INSERT INTO booking_history (booking_id, author_name, action, notes, old_status, new_status)
            VALUES (?, ?, 'NOTA_INTERNA', ?, NULL, NULL)
        ");
        return $histStmt->execute([$id, $author, trim($note)]);
    }

    public function deleteNote(int $noteId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM booking_history WHERE id = ? AND action = 'NOTA_INTERNA'");
        return $stmt->execute([$noteId]);
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
            $sql .= " AND LOWER(b.status) = LOWER(?)";
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

    public function findByRef(string $ref): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM booking_requests WHERE reference_code = ?");
        $stmt->execute([trim($ref)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
