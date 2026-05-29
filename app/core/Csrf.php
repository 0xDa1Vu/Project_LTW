<?php
namespace App\Core;

/**
 * CSRF token cho mọi form POST.
 */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** Trả về thẻ input ẩn để nhúng vào form */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function check(?string $token): bool
    {
        return !empty($_SESSION['_csrf'])
            && is_string($token)
            && hash_equals($_SESSION['_csrf'], $token);
    }

    /** Chặn request nếu token sai (dùng đầu mỗi action POST) */
    public static function verify(): void
    {
        if (!self::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('CSRF token không hợp lệ. Vui lòng tải lại trang.');
        }
    }
}
