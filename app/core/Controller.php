<?php
namespace App\Core;

/**
 * Base Controller: render view, trả JSON, redirect.
 */
abstract class Controller
{
    /**
     * Render view với layout. $data biến thành biến cục bộ trong view.
     * $layout: 'main' (khách) hoặc 'admin'. null = không layout (ajax partial).
     */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = dirname(__DIR__) . "/views/{$view}.php";

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }
        require dirname(__DIR__) . "/views/layouts/{$layout}.php";
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /** Lấy input POST đã trim */
    protected function input(string $key, $default = null)
    {
        $v = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($v) ? trim($v) : $v;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
