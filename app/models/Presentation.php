<?php
class Presentation {
    private $pdo;

    public function __construct($config) {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getByWorkspace($workspaceId) {
        $stmt = $this->pdo->prepare("SELECT * FROM presentations WHERE workspace_id = ? ORDER BY id DESC");
        $stmt->execute([$workspaceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM presentations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO presentations (workspace_id, title, language, theme, version, navigation, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['workspace_id'],
            $data['title'],
            $data['language'],
            $data['theme'],
            $data['version'],
            $data['navigation'] ?? null
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE presentations SET title = ?, language = ?, theme = ?, version = ?, navigation = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([
            $data['title'],
            $data['language'],
            $data['theme'],
            $data['version'],
            $data['navigation'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM presentations WHERE id = ?");
        return $stmt->execute([$id]);
    }
}