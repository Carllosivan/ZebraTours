<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

class RateLimiter
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public static function getClientIp(): string
    {
        $trustProxy = getenv('ZT_TRUST_PROXY');
        if ($trustProxy === '1' && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $first = trim($ips[0]);
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Check if key has exceeded max attempts.
     * Fail-open: if DB fails, returns true so legitimate traffic is not blocked.
     */
    public function check(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        try {
            $now = time();
            $stmt = $this->db->prepare("SELECT hits, reset_at FROM rate_limits WHERE key = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch();

            if (!$row) {
                return true;
            }

            if ($now >= (int) $row['reset_at']) {
                // Window expired
                $del = $this->db->prepare("DELETE FROM rate_limits WHERE key = ?");
                $del->execute([$key]);
                return true;
            }

            return ((int) $row['hits']) < $maxAttempts;
        } catch (Throwable $e) {
            error_log('[RateLimiter Fail-Open Check]: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Record a hit against the rate limiter.
     */
    public function hit(string $key, int $decaySeconds): int
    {
        try {
            $now = time();
            $resetAt = $now + $decaySeconds;

            $stmt = $this->db->prepare("
                INSERT INTO rate_limits (key, hits, reset_at)
                VALUES (?, 1, ?)
                ON CONFLICT(key) DO UPDATE SET
                    hits = CASE WHEN rate_limits.reset_at <= ? THEN 1 ELSE rate_limits.hits + 1 END,
                    reset_at = CASE WHEN rate_limits.reset_at <= ? THEN ? ELSE rate_limits.reset_at END
            ");
            $stmt->execute([$key, $resetAt, $now, $now, $resetAt]);

            $getStmt = $this->db->prepare("SELECT hits FROM rate_limits WHERE key = ?");
            $getStmt->execute([$key]);
            return (int) $getStmt->fetchColumn();
        } catch (Throwable $e) {
            error_log('[RateLimiter Fail-Open Hit]: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clear / reset the rate limiter for a key
     */
    public function reset(string $key): void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE key = ?");
            $stmt->execute([$key]);
        } catch (Throwable $e) {
            error_log('[RateLimiter Reset Error]: ' . $e->getMessage());
        }
    }
}
