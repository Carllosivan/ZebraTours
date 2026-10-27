<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return Session::get('admin_user_id') !== null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'    => Session::get('admin_user_id'),
            'name'  => Session::get('admin_user_name'),
            'email' => Session::get('admin_user_email'),
            'role'  => Session::get('admin_user_role'),
        ];
    }

    public static function login(array $user): void
    {
        Session::set('admin_user_id', $user['id']);
        Session::set('admin_user_name', $user['name']);
        Session::set('admin_user_email', $user['email']);
        Session::set('admin_user_role', $user['role']);
    }

    public static function logout(): void
    {
        Session::remove('admin_user_id');
        Session::remove('admin_user_name');
        Session::remove('admin_user_email');
        Session::remove('admin_user_role');
    }
}
