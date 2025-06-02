<?php
class Workspace {
    private $pdo;

    public function __construct($config) {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM workspaces ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM workspaces WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO workspaces (name, description, language, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['language']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE workspaces SET name = ?, description = ?, language = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['language'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM workspaces WHERE id = ?");
        return $stmt->execute([$id]);
    }
}