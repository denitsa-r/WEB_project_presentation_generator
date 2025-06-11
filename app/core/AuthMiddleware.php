<?php

class AuthMiddleware
{
    public static function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            $baseUrl = dirname(dirname($_SERVER['SCRIPT_NAME']));
            header('Location: ' . $baseUrl . '/public/auth/login');
            exit;
        }
    }

    public static function currentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
