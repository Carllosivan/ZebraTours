<?php
/**
 * Zebra Tours Moçambique - Migração 004 (PHP)
 * Bloqueia utilizadores com palavras-passe inseguras ou padrão
 */

declare(strict_types=1);

return function (\PDO $db): void {
    $knownWeakPasswords = [
        'password',
        'admin123',
        'ZebraAdmin2026!',
        'admin',
        '12345678',
        '123456',
        'secret',
    ];

    $stmt = $db->query("SELECT id, email, password_hash, status FROM users");
    $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $blockStmt = $db->prepare("
        UPDATE users
        SET status = 'bloqueado',
            password_hash = '!',
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");

    foreach ($users as $user) {
        $hash = $user['password_hash'] ?? '';
        // If hash is already invalid marker '!', skip
        if ($hash === '!' || empty($hash)) {
            continue;
        }

        $isWeak = false;
        foreach ($knownWeakPasswords as $weakPass) {
            if (password_verify($weakPass, $hash)) {
                $isWeak = true;
                break;
            }
        }

        if ($isWeak) {
            $blockStmt->execute([$user['id']]);
            // Log block event if audit_logs table exists
            try {
                $auditStmt = $db->prepare("
                    INSERT INTO audit_logs (user_id, action, entity_type, entity_id, metadata)
                    VALUES (?, 'MIGRACAO_004_BLOQUEADO', 'users', ?, 'Conta bloqueada por possuir palavra-passe insegura/padrão')
                ");
                $auditStmt->execute([$user['id'], $user['id']]);
            } catch (\Throwable $e) {
                // Ignore if audit_logs not ready yet
            }
        }
    }
};
