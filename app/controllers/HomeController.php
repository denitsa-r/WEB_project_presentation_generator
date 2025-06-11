<?php

require_once __DIR__ . '/../core/AuthMiddleware.php';

class HomeController extends Controller
{
    public function index()
    {
        AuthMiddleware::requireLogin();
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}
