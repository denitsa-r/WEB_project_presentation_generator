<?php
class Slide {
    private $pdo;

    public function __construct($config) {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getByPresentation($presentationId) {
        $stmt = $this->pdo->prepare("SELECT * FROM slides WHERE presentation_id = ? ORDER BY slide_order ASC");
        $stmt->execute([$presentationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM slides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}