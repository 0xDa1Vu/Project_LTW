<?php
namespace App\Core;

/**
 * Router đơn giản: map (method, pattern) -> [ControllerClass, action].
 * Pattern hỗ trợ param dạng {id} (số) và {slug} (chuỗi).
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $regex = $this->toRegex($route);
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $handler;
                $controller = new $class();
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        http_response_code(404);
        (new \App\Controllers\HomeController())->notFound();
    }

    private function toRegex(string $route): string
    {
        $regex = preg_replace('#\{id\}#', '(?P<id>\d+)', $route);
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $regex);
        return '#^' . $regex . '$#';
    }
}
