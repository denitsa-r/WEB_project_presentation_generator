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

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO slides (presentation_id, slide_order, type, layout, style, content, navigation, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['presentation_id'],
            $data['slide_order'],
            $data['type'],
            $data['layout'] ?? null,
            $data['style'] ?? 'light',
            $data['content'] ?? '',
            $data['navigation'] ?? null
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE slides SET slide_order = ?, type = ?, layout = ?, style = ?, content = ?, navigation = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([
            $data['slide_order'],
            $data['type'],
            $data['layout'] ?? null,
            $data['style'] ?? 'light',
            $data['content'] ?? '',
            $data['navigation'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM slides WHERE id = ?");
        return $stmt->execute([$id]);
    }
}