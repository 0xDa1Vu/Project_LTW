<?php
/**
 * Front controller — mọi request đi qua đây.
 */
require dirname(__DIR__) . '/app/bootstrap.php';

/** @var \App\Core\Router $router */
$router = require dirname(__DIR__) . '/app/routes.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
