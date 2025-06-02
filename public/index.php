<?php
session_start();

$config = require __DIR__ . '/../config/config.php';

$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$urlParts = explode('/', $url);

$controllerName = ucfirst(array_shift($urlParts)) . 'Controller';
$method = array_shift($urlParts) ?: 'index';

$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName($config);
        if (method_exists($controller, $method)) {
            // Извикваме метода с параметрите от URL
            call_user_func_array([$controller, $method], $urlParts);
        } else {
            http_response_code(404);
            echo "Методът $method не е намерен.";
        }
    } else {
        http_response_code(404);
        echo "Класът $controllerName не е намерен.";
    }
} else {
    http_response_code(404);
    echo "Файлът с контролер $controllerFile не е намерен.";
}