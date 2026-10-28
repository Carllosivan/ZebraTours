<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use Throwable;

class Auth
{
    private static ?array $cachedUser = null;

    public static function check(): bool
    {
        $userId = Session::get('admin_user_id');
        if ($userId === null) {
            self::$cachedUser = null;
            return false;
        }

        // Revalidate against the database that user still exists and is 'ativo'
        try {
            $userModel = new User();
            $user = $userModel->findById((int) $userId);

            if (!$user || ($user['status'] ?? '') !== 'ativo') {
                self::logout();
                return false;
            }

            self::$cachedUser = $user;
            return true;
        } catch (Throwable $e) {
            error_log('[Auth Check Error]: ' . $e->getMessage());
            return false;
        }
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return self::$cachedUser ?? [
            'id'    => Session::get('admin_user_id'),
            'name'  => Session::get('admin_user_name'),
            'email' => Session::get('admin_user_email'),
            'role'  => Session::get('admin_user_role'),
        ];
    }

    public static function login(array $user): void
    {
        // Regenerate session ID and create fresh CSRF token
        Session::regenerate(true);

        Session::set('admin_user_id', $user['id']);
        Session::set('admin_user_name', $user['name']);
        Session::set('admin_user_email', $user['email']);
        Session::set('admin_user_role', $user['role']);

        self::$cachedUser = $user;
    }

    public static function logout(): void
    {
        self::$cachedUser = null;
        Session::destroy();
    }

    public static function hasRole(string|array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        $currentRole = $user['role'] ?? '';
        $allowed = is_array($roles) ? $roles : [$roles];

        // superadmin always satisfies admin permissions
        if ($currentRole === 'superadmin') {
            return true;
        }

        return in_array($currentRole, $allowed, true);
    }
}
