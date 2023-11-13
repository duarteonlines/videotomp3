<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/router.php';
require_once __DIR__ . "/../middlewares/cors.php";

try {
    cors();
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $request = $_SERVER['REQUEST_METHOD'];

    if (!isset($router[$request])) {
        throw new Exception("A rota não existe");
    }

    if (!array_key_exists($uri, $router[$request])) {
        throw new Exception("A rota não existe");
    }

    $controller = $router[$request][$uri];
    $controller();
} catch (Excpetion $e) {
    echo $e->getMessage();
}
