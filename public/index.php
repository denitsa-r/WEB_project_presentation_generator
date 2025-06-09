<?php
require_once '../config/init.php';
require_once '../app/helpers/Logger.php';
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';
require_once '../app/core/AuthMiddleware.php';
require_once '../app/controllers/SlidesController.php';

require_once '../config/config.php';

// Проверка за коренния URL и пренасочване към страницата за вход
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

if ($request_uri === $base_path || $request_uri === $base_path . '/') {
    header('Location: ' . $base_path . '/auth/login');
    exit;
}

$app = new App();
