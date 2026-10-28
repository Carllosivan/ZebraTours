<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function getDatabasePath(): string
    {
        $envPath = getenv('ZT_DB_PATH');
        if (!empty($envPath)) {
            return $envPath;
        }
        return dirname(__DIR__, 2) . '/database/zebratours.db';
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dbPath = self::getDatabasePath();
            $dir = dirname($dbPath);

            // Check if directory exists or can be created
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                    throw new RuntimeException("A diretoria da base de dados ({$dir}) não existe e não pôde ser criada. Verifique as permissões do sistema.");
                }
            }

            // Check if directory is writable (SQLite WAL mode creates -wal and -shm in the same directory)
            if (!is_writable($dir)) {
                throw new RuntimeException("A diretoria da base de dados ({$dir}) não tem permissões de escrita. O SQLite necessita de gravar na diretoria para os ficheiros WAL.");
            }

            // Check if file exists and is writable
            if (file_exists($dbPath) && !is_writable($dbPath)) {
                throw new RuntimeException("O ficheiro da base de dados ({$dbPath}) não tem permissões de escrita para o servidor web.");
            }

            try {
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;");
                self::$instance->exec("PRAGMA journal_mode = WAL;");
            } catch (PDOException $e) {
                throw new RuntimeException("Erro de ligação à base de dados SQLite em {$dbPath}: " . $e->getMessage());
            }

            // Auto-run migrations if enabled
            if (getenv('ZT_AUTO_MIGRATE') !== '0') {
                try {
                    require_once __DIR__ . '/Migrator.php';
                    $migrator = new Migrator(self::$instance);
                    $migrator->run();
                } catch (\Throwable $e) {
                    error_log('[Migrator Error]: ' . $e->getMessage());
                }
            }
        }

        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
