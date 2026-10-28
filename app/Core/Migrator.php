<?php
/**
 * Zebra Tours Moçambique - Migrator
 * Pure native PHP 8.2 Migration Runner with automatic baselining
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

class Migrator
{
    private PDO $db;
    private string $migrationsDir;
    private string $schemaFile;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->migrationsDir = dirname(__DIR__, 2) . '/database/migrations';
        $this->schemaFile = dirname(__DIR__, 2) . '/database/schema.sql';
    }

    public function run(): array
    {
        $appliedNow = [];

        // 1. Ensure schema_migrations table exists
        $hasMigrationsTable = (bool) $this->db->query("
            SELECT count(*) FROM sqlite_master WHERE type='table' AND name='schema_migrations'
        ")->fetchColumn();

        if (!$hasMigrationsTable) {
            // Check if existing database with data (baseline check)
            $hasPackagesTable = (bool) $this->db->query("
                SELECT count(*) FROM sqlite_master WHERE type='table' AND name='packages'
            ")->fetchColumn();

            $this->db->exec("
                CREATE TABLE IF NOT EXISTS schema_migrations (
                    migration TEXT PRIMARY KEY,
                    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");

            if ($hasPackagesTable) {
                // Baseline: existing DB has tables; mark 000 and 002 as already applied
                $stmt = $this->db->prepare("INSERT OR IGNORE INTO schema_migrations (migration) VALUES (?)");
                $stmt->execute(['000_schema.sql']);
                $stmt->execute(['002_premium_cms_tables.sql']);
            }
        }

        // 2. Check if 000_schema.sql needs to run
        $applied = $this->getAppliedMigrations();

        if (!in_array('000_schema.sql', $applied, true)) {
            if (file_exists($this->schemaFile)) {
                $sql = file_get_contents($this->schemaFile);
                if ($sql) {
                    $this->db->exec($sql);
                }
            }
            $this->recordMigration('000_schema.sql');
            $appliedNow[] = '000_schema.sql';
            $applied[] = '000_schema.sql';
        }

        // 3. Scan migrations directory
        if (is_dir($this->migrationsDir)) {
            $files = scandir($this->migrationsDir) ?: [];
            sort($files);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || in_array($file, $applied, true)) {
                    continue;
                }

                $filePath = $this->migrationsDir . '/' . $file;
                if (!is_file($filePath)) {
                    continue;
                }

                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext === 'sql') {
                    $sql = file_get_contents($filePath);
                    if ($sql) {
                        $this->db->exec($sql);
                    }
                    $this->recordMigration($file);
                    $appliedNow[] = $file;
                    $applied[] = $file;
                } elseif ($ext === 'php') {
                    $migrationCallable = require $filePath;
                    if (is_callable($migrationCallable)) {
                        $migrationCallable($this->db);
                    }
                    $this->recordMigration($file);
                    $appliedNow[] = $file;
                    $applied[] = $file;
                }
            }
        }

        return $appliedNow;
    }

    public function getAppliedMigrations(): array
    {
        $hasTable = (bool) $this->db->query("
            SELECT count(*) FROM sqlite_master WHERE type='table' AND name='schema_migrations'
        ")->fetchColumn();

        if (!$hasTable) {
            return [];
        }

        $stmt = $this->db->query("SELECT migration FROM schema_migrations ORDER BY migration ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function getPendingMigrations(): array
    {
        $applied = $this->getAppliedMigrations();
        $pending = [];

        if (!in_array('000_schema.sql', $applied, true)) {
            $pending[] = '000_schema.sql';
        }

        if (is_dir($this->migrationsDir)) {
            $files = scandir($this->migrationsDir) ?: [];
            sort($files);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (($ext === 'sql' || $ext === 'php') && !in_array($file, $applied, true)) {
                    $pending[] = $file;
                }
            }
        }

        return $pending;
    }

    private function recordMigration(string $migration): void
    {
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO schema_migrations (migration) VALUES (?)");
        $stmt->execute([$migration]);
    }
}
