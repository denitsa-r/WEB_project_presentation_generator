<?php

class Slide extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByPresentation($presentationId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM slides WHERE presentation_id = ? ORDER BY slide_order ASC",
            [$presentationId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->query(
            "SELECT * FROM slides WHERE id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $this->db->query(
                "INSERT INTO slides (presentation_id, slide_order, type, layout, style, content, navigation, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [
                    $data['presentation_id'],
                    $data['slide_order'],
                    $data['type'],
                    $data['layout'] ?? null,
                    $data['style'] ?? 'light',
                    $data['content'] ?? '',
                    $data['navigation'] ?? null
                ]
            );
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $data)
    {
        $stmt = $this->db->query(
            "UPDATE slides SET slide_order = ?, type = ?, layout = ?, style = ?, content = ?, navigation = ?, updated_at = NOW() WHERE id = ?",
            [
                $data['slide_order'],
                $data['type'],
                $data['layout'] ?? null,
                $data['style'] ?? 'light',
                $data['content'] ?? '',
                $data['navigation'] ?? null,
                $id
            ]
        );
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->query("DELETE FROM slides WHERE id = ?", [$id]);
        return $stmt->execute();
    }
}