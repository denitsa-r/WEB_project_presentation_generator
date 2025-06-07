<?php

class WorkspaceController
{
    public function delete($id)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = new Workspace();
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $id)) {
            $this->redirect('workspace/index', ['error' => 'Нямате права за изтриване на това работно пространство']);
        }

        if ($workspaceModel->delete($id)) {
            $this->redirect('workspace/index', ['success' => 'Работното пространство е изтрито успешно']);
        } else {
            $this->redirect('workspace/index', ['error' => 'Възникна грешка при изтриването на работното пространство']);
        }
    }

    public function share($id)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = new Workspace();
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $id)) {
            $this->redirect('workspace/index', ['error' => 'Нямате права за споделяне на това работно пространство']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'member';

            $result = $workspaceModel->shareWorkspace($id, $email, $role);
            
            if ($result['success']) {
                $this->redirect('workspace/view/' . $id, ['success' => $result['message']]);
            } else {
                $this->redirect('workspace/view/' . $id, ['error' => $result['message']]);
            }
        }

        $workspace = $workspaceModel->getById($id);
        $members = $workspaceModel->getWorkspaceMembers($id);

        $this->view('workspace/share', [
            'workspace' => $workspace,
            'members' => $members
        ]);
    }

    public function removeMember($workspaceId, $userId)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = new Workspace();
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $workspaceId)) {
            $this->redirect('workspace/index', ['error' => 'Нямате права за премахване на членове']);
        }

        $result = $workspaceModel->removeAccess($workspaceId, $userId);
        
        if ($result['success']) {
            $this->redirect('workspace/share/' . $workspaceId, ['success' => $result['message']]);
        } else {
            $this->redirect('workspace/share/' . $workspaceId, ['error' => $result['message']]);
        }
    }

    public function updateMemberRole($workspaceId, $userId)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = new Workspace();
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $workspaceId)) {
            $this->redirect('workspace/index', ['error' => 'Нямате права за промяна на роли']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newRole = $_POST['role'] ?? '';
            
            if (!in_array($newRole, ['member', 'editor'])) {
                $this->redirect('workspace/share/' . $workspaceId, ['error' => 'Невалидна роля']);
            }

            $result = $workspaceModel->updateMemberRole($workspaceId, $userId, $newRole);
            
            if ($result['success']) {
                $this->redirect('workspace/share/' . $workspaceId, ['success' => $result['message']]);
            } else {
                $this->redirect('workspace/share/' . $workspaceId, ['error' => $result['message']]);
            }
        }

        $this->redirect('workspace/share/' . $workspaceId, ['error' => 'Невалидна заявка']);
    }

    public function view($id)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = new Workspace();
        $workspace = $workspaceModel->getById($id);
        
        if (!$workspace) {
            $this->redirect('workspace/index', ['error' => 'Работното пространство не е намерено']);
        }

        if (!$workspaceModel->hasAccess($_SESSION['user_id'], $id)) {
            $this->redirect('workspace/index', ['error' => 'Нямате достъп до това работно пространство']);
        }

        $isOwner = $workspaceModel->isOwner($_SESSION['user_id'], $id);
        $presentations = $workspaceModel->getWorkspacePresentations($id);

        $this->view('workspace/view', [
            'workspace' => $workspace,
            'isOwner' => $isOwner,
            'presentations' => $presentations
        ]);
    }
} 