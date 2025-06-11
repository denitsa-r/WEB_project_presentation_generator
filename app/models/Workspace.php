<?php

require_once __DIR__ . '/User.php';

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

    public function update($id, $name)
    {
        try {
            $this->db->query(
                "UPDATE workspaces SET name = ? WHERE id = ?",
                [$name, $id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $this->db->query(
                "DELETE FROM workspaces WHERE id = ?",
                [$id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isOwner($userId, $workspaceId)
    {
        $stmt = $this->db->query(
            "SELECT 1 FROM user_workspaces WHERE user_id = ? AND workspace_id = ? AND role = 'owner'",
            [$userId, $workspaceId]
        );
        return $stmt->fetch() !== false;
    }

    public function shareWorkspace($workspaceId, $email, $role = 'viewer')
    {
        try {
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Потребителят не съществува'];
            }

            if ($this->hasAccess($user['id'], $workspaceId)) {
                return ['success' => false, 'message' => 'Потребителят вече има достъп до това работно пространство'];
            }

            $this->db->query(
                "INSERT INTO user_workspaces (workspace_id, user_id, role) VALUES (?, ?, ?)",
                [$workspaceId, $user['id'], $role]
            );

            return ['success' => true, 'message' => 'Работното пространство е споделено успешно'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Възникна грешка при споделянето'];
        }
    }

    public function removeAccess($workspaceId, $userId)
    {
        try {
            if ($this->isOwner($userId, $workspaceId)) {
                return ['success' => false, 'message' => 'Не можете да премахнете собственика на работното пространство'];
            }

            $this->db->query(
                "DELETE FROM user_workspaces WHERE workspace_id = ? AND user_id = ?",
                [$workspaceId, $userId]
            );

            return ['success' => true, 'message' => 'Достъпът е премахнат успешно'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Възникна грешка при премахването на достъпа'];
        }
    }

    public function getWorkspaceMembers($workspaceId)
    {
        $stmt = $this->db->query(
            "SELECT u.id as user_id, u.email, uw.role 
            FROM users u 
            JOIN user_workspaces uw ON u.id = uw.user_id 
            WHERE uw.workspace_id = ?",
            [$workspaceId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeMember($workspaceId, $userId)
    {
        try {
            if ($this->isOwner($userId, $workspaceId)) {
                return [
                    'success' => false,
                    'message' => 'Не можете да премахнете собственика на работното пространство'
                ];
            }

            $stmt = $this->db->prepare("DELETE FROM user_workspaces WHERE workspace_id = ? AND user_id = ?");
            $stmt->execute([$workspaceId, $userId]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Членът е премахнат успешно'
                ];
            }

            return [
                'success' => false,
                'message' => 'Грешка при премахване на члена'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Грешка при премахване на члена: ' . $e->getMessage()
            ];
        }
    }

    public function getWorkspacePresentations($workspaceId)
    {
        $stmt = $this->db->query(
            "SELECT p.*, u.first_name, u.last_name 
            FROM presentations p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.workspace_id = ? 
            ORDER BY p.created_at DESC",
            [$workspaceId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 