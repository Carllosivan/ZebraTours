<?php
/**
 * Zebra Tours Moçambique - CLI de Backup da Base de Dados SQLite
 * Execução: php bin/backup.php
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

$sourceDb = Database::getDatabasePath();
if (!file_exists($sourceDb)) {
    echo "ERRO: Base de dados não encontrada em {$sourceDb}\n";
    exit(1);
}

$backupDir = dirname($sourceDb) . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0775, true);
}

$destFile = $backupDir . '/zebratours_backup_' . date('Ymd_His') . '.db';

// Use SQLite VACUUM INTO for consistent online snapshot without locking issues
try {
    $db = Database::getConnection();
    // Escape single quotes for SQL literal
    $escapedPath = str_replace("'", "''", $destFile);
    $db->exec("VACUUM INTO '{$escapedPath}';");
    echo "SUCESSO: Cópia de segurança criada em: {$destFile}\n";
} catch (\Throwable $e) {
    // Fallback to copy if older SQLite
    if (copy($sourceDb, $destFile)) {
        echo "SUCESSO (cópia simples): Backup criado em: {$destFile}\n";
    } else {
        echo "ERRO ao criar backup: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Rotation: Keep last 7 backups, purge older ones
$files = glob($backupDir . '/zebratours_backup_*.db');
if ($files && count($files) > 7) {
    sort($files); // Oldest first
    $toDelete = count($files) - 7;
    for ($i = 0; $i < $toDelete; $i++) {
        @unlink($files[$i]);
        echo "ROTAÇÃO: Backup antigo removido: " . basename($files[$i]) . "\n";
    }
}

echo "Operação de backup concluída.\n";
exit(0);
