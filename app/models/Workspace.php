<?php

class Workspace extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create($name, $userId)
    {
        try {
            $this->db->query(
                "INSERT INTO workspaces (name) VALUES (?)",
                [$name]
            );
            
            $workspaceId = $this->db->lastInsertId();
            
            // Add creator as owner
            $this->db->query(
                "INSERT INTO user_workspaces (workspace_id, user_id, role) VALUES (?, ?, 'owner')",
                [$workspaceId, $userId]
            );
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserWorkspaces($userId)
    {
        $stmt = $this->db->query(
            "SELECT w.*, uw.role 
            FROM workspaces w 
            JOIN user_workspaces uw ON w.id = uw.workspace_id 
            WHERE uw.user_id = ? 
            ORDER BY w.created_at DESC",
            [$userId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->query(
            "SELECT * FROM workspaces WHERE id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasAccess($userId, $workspaceId)
    {
        $stmt = $this->db->query(
            "SELECT 1 FROM user_workspaces WHERE user_id = ? AND workspace_id = ?",
            [$userId, $workspaceId]
        );
        return $stmt->fetch() !== false;
    }
} 