<?php
/**
 * Zebra Tours Moçambique - CLI de Gestão de Palavras-passe
 * Uso: php bin/admin-password.php <email> [--name=Nome] [--role=superadmin|admin|editor]
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
use App\Models\User;

$args = $argv;
array_shift($args); // remove script name

$email = null;
$name = null;
$role = 'superadmin';

foreach ($args as $arg) {
    if (str_starts_with($arg, '--name=')) {
        $name = substr($arg, 7);
    } elseif (str_starts_with($arg, '--role=')) {
        $r = substr($arg, 7);
        if (in_array($r, ['superadmin', 'admin', 'editor'], true)) {
            $role = $r;
        } else {
            echo "ERRO: Papel inválido. Valores permitidos: superadmin, admin, editor.\n";
            exit(1);
        }
    } elseif (!str_starts_with($arg, '--') && $email === null) {
        $email = strtolower(trim($arg));
    }
}

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Uso: php bin/admin-password.php <email_valido> [--name=Nome] [--role=superadmin|admin|editor]\n";
    echo "Exemplo: php bin/admin-password.php admin@zebratours.co.mz --name=\"Administrador\"\n";
    exit(1);
}

// Obtain password
$password = getenv('ZT_NEW_PASSWORD');
if (!$password) {
    echo "Definir palavra-passe para {$email}:\n";
    if (stripos(PHP_OS, 'WIN') === 0) {
        echo "Digite a palavra-passe (mín. 12 carateres): ";
        $password = trim((string) fgets(STDIN));
    } else {
        echo "Digite a palavra-passe (mín. 12 carateres, entrada oculta): ";
        $password = trim((string) shell_exec('stty -echo; head -n 1; stty echo'));
        echo "\n";
    }
}

$len = mb_strlen($password);
if ($len < 12) {
    echo "ERRO: A palavra-passe tem de ter no mínimo 12 carateres (foram fornecidos {$len}).\n";
    exit(1);
}

$weakList = ['password', 'admin123', 'ZebraAdmin2026!', 'admin', '12345678', '123456789012', 'administrator'];
if (in_array(strtolower($password), array_map('strtolower', $weakList), true)) {
    echo "ERRO: Esta palavra-passe é conhecida e insegura. Escolha uma palavra-passe forte e exclusiva.\n";
    exit(1);
}

try {
    $db = Database::getConnection();
    $userModel = new User($db);
    $existing = $userModel->findByEmail($email);

    if ($existing) {
        $userModel->updatePassword((int) $existing['id'], $password);
        if ($name) {
            $userModel->update((int) $existing['id'], ['name' => $name]);
        }
        echo "SUCESSO: Palavra-passe atualizada para o utilizador existente '{$email}' (Estado: ativo, Papel: {$existing['role']}).\n";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newId = $userModel->create([
            'name'          => $name ?: 'Administrador Zebra Tours',
            'email'         => $email,
            'password_hash' => $hash,
            'role'          => $role,
            'status'        => 'ativo',
        ]);
        echo "SUCESSO: Novo utilizador '{$email}' criado com sucesso (ID: {$newId}, Papel: {$role}, Estado: ativo).\n";
    }
    exit(0);
} catch (\Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
