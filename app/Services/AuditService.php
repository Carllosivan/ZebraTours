<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use PDO;
use Throwable;

class AuditService
{
    private static bool $columnsChecked = false;

    public static function log(
        string $action,
        ?int $userId = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $metadata = null
    ): void {
        try {
            $db = Database::getConnection();
            $ip = RateLimiter::getClientIp();
            $ua = mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

            if (!self::$columnsChecked) {
                $cols = $db->query("PRAGMA table_info(audit_logs)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('ip_address', $cols, true)) {
                    @$db->exec("ALTER TABLE audit_logs ADD COLUMN ip_address TEXT;");
                }
                if (!in_array('user_agent', $cols, true)) {
                    @$db->exec("ALTER TABLE audit_logs ADD COLUMN user_agent TEXT;");
                }
                self::$columnsChecked = true;
            }

            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, entity_type, entity_id, metadata, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                strtoupper(trim($action)),
                $entityType,
                $entityId,
                $metadata ? mb_substr($metadata, 0, 1000) : null,
                $ip,
                $ua
            ]);
        } catch (Throwable $e) {
            // Resilient fallback: store IP and UA directly in metadata if custom columns are unavailable
            try {
                $metaCombined = trim(($metadata ?: '') . " [IP: {$ip}, UA: {$ua}]");
                $fallbackStmt = $db->prepare("
                    INSERT INTO audit_logs (user_id, action, entity_type, entity_id, metadata)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $fallbackStmt->execute([
                    $userId,
                    strtoupper(trim($action)),
                    $entityType,
                    $entityId,
                    mb_substr($metaCombined, 0, 1000),
                ]);
            } catch (Throwable $e2) {
                error_log('[AuditService Warning]: ' . $e2->getMessage());
            }
        }
    }
}
