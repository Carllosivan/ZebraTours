<?php
/**
 * Zebra Tours Moçambique - Diagnóstico do Sistema (CLI Doctor)
 * Verifica requisitos de ambiente, permissões, extensões e estado da base de dados.
 *
 * Execução: php bin/doctor.php
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Acesso exclusivo por linha de comando (CLI).\n";
    exit(1);
}

require dirname(__DIR__) . '/app/Core/helpers.php';

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;
use App\Core\Migrator;

echo "\n========================================================\n";
echo " ZEBRA TOURS MOÇAMBIQUE - DIAGNÓSTICO DO SISTEMA (CLI)\n";
echo "========================================================\n\n";

$allPassed = true;
$warnings = [];

function check(string $label, bool $condition, string $failMessage): bool {
    global $allPassed;
    if ($condition) {
        echo " [ OK ]    " . str_pad($label, 45, '.') . " PASSOU\n";
        return true;
    } else {
        echo " [FALHA]   " . str_pad($label, 45, '.') . " ERRO\n";
        echo "           -> " . $failMessage . "\n";
        $allPassed = false;
        return false;
    }
}

function warn(string $label, string $warnMessage): void {
    global $warnings;
    echo " [AVISO]   " . str_pad($label, 45, '.') . " ALERTA\n";
    echo "           -> " . $warnMessage . "\n";
    $warnings[] = "{$label}: {$warnMessage}";
}

// 1. PHP Version
$phpVersion = PHP_VERSION;
check(
    "Versão do PHP (>= 8.2)",
    version_compare($phpVersion, '8.2.0', '>='),
    "Versão instalada: {$phpVersion}. O Zebra Tours requer PHP 8.2 ou superior."
);

// 2. Extensões Essenciais
check(
    "Extensão pdo_sqlite",
    extension_loaded('pdo_sqlite'),
    "Instale o pacote php-sqlite3 (Debian/Ubuntu: apt-get install php-sqlite3)."
);

check(
    "Extensão json",
    extension_loaded('json'),
    "Instale a extensão php-json."
);

check(
    "Extensão session",
    extension_loaded('session'),
    "A extensão nativa de sessões do PHP não está ativa."
);

// 3. mbstring Check (com polyfill)
if (extension_loaded('mbstring')) {
    echo " [ OK ]    " . str_pad("Extensão mbstring nativa", 45, '.') . " INSTALADA\n";
} else {
    warn(
        "Extensão mbstring",
        "Extensão nativa ausente. O sistema está a usar os polyfills nativos em app/Core/helpers.php. Recomendado: apt-get install php-mbstring."
    );
}

// 4. Base de Dados SQLite e Diretoria
$dbPath = Database::getDatabasePath();
$dbDir = dirname($dbPath);

check(
    "Diretoria da base de dados existe",
    is_dir($dbDir) || @mkdir($dbDir, 0775, true),
    "A pasta {$dbDir} não existe e não pôde ser criada. Crie a pasta e ajuste permissões."
);

check(
    "Diretoria da base de dados tem permissão de escrita",
    is_writable($dbDir),
    "A pasta {$dbDir} NÃO é gravável. O SQLite WAL necessita de gravar na diretoria (execute: chmod 775 {$dbDir} && chown www-data:www-data {$dbDir})."
);

if (file_exists($dbPath)) {
    check(
        "Ficheiro zebratours.db tem permissão de escrita",
        is_writable($dbPath),
        "O ficheiro {$dbPath} NÃO é gravável pelo servidor web (execute: chmod 664 {$dbPath})."
    );
} else {
    echo " [INFO]    " . str_pad("Ficheiro da BD ainda não criado", 45, '.') . " NOVO\n";
}

// 5. Diretoria de Uploads Multimédia
$uploadDir = dirname(__DIR__) . '/public/uploads/multimedia';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}
check(
    "Diretoria de uploads gravável",
    is_dir($uploadDir) && is_writable($uploadDir),
    "A pasta {$uploadDir} não é gravável para o upload de fotos. Execute: chmod 775 {$uploadDir}."
);

// 6. Diretoria de Sessões PHP
$sessionPath = session_save_path() ?: sys_get_temp_dir();
check(
    "Diretoria de sessões do PHP gravável",
    is_dir($sessionPath) && is_writable($sessionPath),
    "A pasta de sessões ({$sessionPath}) não tem permissões de escrita."
);

// 7. Estado das Migrações da Base de Dados
try {
    $db = Database::getConnection();
    $migrator = new Migrator($db);
    $applied = $migrator->getAppliedMigrations();
    $pending = $migrator->getPendingMigrations();

    echo " [ OK ]    " . str_pad("Migrações aplicadas na BD", 45, '.') . " " . count($applied) . " REGISTADAS\n";
    if (empty($pending)) {
        echo " [ OK ]    " . str_pad("Migrações pendentes", 45, '.') . " NENHUMA\n";
    } else {
        warn("Migrações pendentes", count($pending) . " ficheiros por aplicar: " . implode(', ', $pending));
    }
} catch (\Throwable $e) {
    check("Ligação e migrações da base de dados", false, "Falha de ligação: " . $e->getMessage());
}

echo "\n--------------------------------------------------------\n";
if ($allPassed) {
    echo " RESULTADO: O AMBIENTE ESTÁ PRONTO E SEGURO (OK)\n";
    echo " O site e o painel Zebra Tours estão operacionais.\n";
    echo "--------------------------------------------------------\n\n";
    exit(0);
} else {
    echo " RESULTADO: FORAM ENCONTRADOS PROBLEMAS CRÍTICOS (FALHA)\n";
    echo " Corrija os pontos indicados acima antes de colocar em produção.\n";
    echo "--------------------------------------------------------\n\n";
    exit(1);
}
