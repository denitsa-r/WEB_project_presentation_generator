<?php

class Database {
    private $pdo;

    public function __construct() {
        try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
        } catch (PDOException $e) {
            die("Query Error: " . $e->getMessage());
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
