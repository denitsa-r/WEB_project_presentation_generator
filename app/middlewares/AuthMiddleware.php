<?php

class AuthMiddleware {
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function currentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
} 