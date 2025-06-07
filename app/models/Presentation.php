<?php

class Presentation extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByWorkspaceId($workspaceId)
    {
        $sql = "SELECT * FROM presentations WHERE workspace_id = :workspace_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['workspace_id' => $workspaceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($workspaceId, $title, $language = 'bg', $theme = 'light')
    {
        try {
            $sql = "INSERT INTO presentations (workspace_id, title, language, theme) 
                    VALUES (:workspace_id, :title, :language, :theme)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'workspace_id' => $workspaceId,
                'title' => $title,
                'language' => $language,
                'theme' => $theme
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM presentations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $language, $theme)
    {
        try {
            $sql = "UPDATE presentations SET title = :title, language = :language, theme = :theme 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'title' => $title,
                'language' => $language,
                'theme' => $theme
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error updating presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            // Първо изтриваме всички слайдове, свързани с презентацията
            $sql = "DELETE FROM slides WHERE presentation_id = :presentation_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['presentation_id' => $id]);
            
            // След това изтриваме самата презентация
            $sql = "DELETE FROM presentations WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function hasAccess($userId, $presentationId)
    {
        // Проверяваме дали потребителят е собственик на презентацията
        $stmt = $this->db->query(
            "SELECT 1 FROM presentations WHERE id = ? AND user_id = ?",
            [$presentationId, $userId]
        );
        if ($stmt->fetch()) {
            return true;
        }

        // Проверяваме дали потребителят има достъп чрез работното пространство
        $stmt = $this->db->query(
            "SELECT 1 FROM presentations p
            JOIN workspaces w ON p.workspace_id = w.id
            JOIN user_workspaces uw ON w.id = uw.workspace_id
            WHERE p.id = ? AND uw.user_id = ?",
            [$presentationId, $userId]
        );
        return $stmt->fetch() !== false;
    }
} 