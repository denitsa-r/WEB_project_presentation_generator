<?php

//header('Location: /WEB_project_presentation_generator/public/auth/login');

$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize
$baseUrl = rtrim($baseUrl, '/');
$requestUri = rtrim($requestUri, '/');

// If the user accessed the root of the app
if ($requestUri === $baseUrl) {
    header('Location: ' . $baseUrl . '/public/auth/login');
    exit;
}
exit; 