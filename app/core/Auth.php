<?php
namespace App\Core;

use App\Models\User;

/**
 * Xác thực + phân quyền. Lưu user id trong session.
 */
class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = (new User())->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            Session::set('user_id', (int) $user['id']);
            Session::set('user_role', $user['role']);
            Session::set('user_name', $user['name']);
            return true;
        }
        return false;
    }

    public static function login(array $user): void
    {
        Session::set('user_id', (int) $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);
    }

    public static function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_role');
        Session::remove('user_name');
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        return Session::has('user_id') ? (int) Session::get('user_id') : null;
    }

    public static function user(): ?array
    {
        $id = self::id();
        return $id ? (new User())->find($id) : null;
    }

    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }

    /** Yêu cầu đăng nhập; nếu chưa thì redirect /login */
    public static function require(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            header('Location: /login');
            exit;
        }
    }

    /** Yêu cầu vai trò cụ thể (vd admin) */
    public static function requireRole(string $role): void
    {
        self::require();
        if (Session::get('user_role') !== $role) {
            http_response_code(403);
            exit('403 — Bạn không có quyền truy cập trang này.');
        }
    }
}
