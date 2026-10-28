<?php
/**
 * Zebra Tours Moçambique - CLI de Retenção e Limpeza de Dados
 * Remove rate limits expirados e histórico de auditoria com mais de 90 dias
 *
 * Execução: php bin/retention.php
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Acesso exclusivo por CLI.\n";
    exit(1);
}

require dirname(__DIR__) . '/app/Core/helpers.php';
require dirname(__DIR__) . '/app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();

    // 1. Clean expired rate limits
    $now = time();
    $rateStmt = $db->prepare("DELETE FROM rate_limits WHERE reset_at < ?");
    $rateStmt->execute([$now]);
    $cleanedRates = $rateStmt->rowCount();
    echo "Limpeza de Rate Limits: {$cleanedRates} registos expirados removidos.\n";

    // 2. Clean old audit logs (>90 days)
    $ninetyDaysAgo = date('Y-m-d H:i:s', strtotime('-90 days'));
    $auditStmt = $db->prepare("DELETE FROM audit_logs WHERE created_at < ?");
    $auditStmt->execute([$ninetyDaysAgo]);
    $cleanedAudit = $auditStmt->rowCount();
    echo "Limpeza de Auditoria: {$cleanedAudit} registos com mais de 90 dias removidos.\n";

    echo "Operação de retenção concluída com sucesso.\n";
    exit(0);
} catch (\Throwable $e) {
    echo "ERRO na retenção de dados: " . $e->getMessage() . "\n";
    exit(1);
}
