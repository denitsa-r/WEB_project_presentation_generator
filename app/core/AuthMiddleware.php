<?php

class AuthMiddleware
{
    public static function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    public static function currentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
