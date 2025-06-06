<?php

class Presentation extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByWorkspaceId($workspaceId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM presentations WHERE workspace_id = ? ORDER BY created_at DESC",
            [$workspaceId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($workspaceId, $title, $language = 'bg', $theme = 'light')
    {
        try {
            $this->db->query(
                "INSERT INTO presentations (workspace_id, title, language, theme) VALUES (?, ?, ?, ?)",
                [$workspaceId, $title, $language, $theme]
            );
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById($id)
    {
        $stmt = $this->db->query(
            "SELECT * FROM presentations WHERE id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $language, $theme)
    {
        try {
            $this->db->query(
                "UPDATE presentations SET title = ?, language = ?, theme = ? WHERE id = ?",
                [$title, $language, $theme, $id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            // Първо изтриваме всички слайдове, свързани с презентацията
            $this->db->query(
                "DELETE FROM slides WHERE presentation_id = ?",
                [$id]
            );
            
            // След това изтриваме самата презентация
            $this->db->query(
                "DELETE FROM presentations WHERE id = ?",
                [$id]
            );
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
} 