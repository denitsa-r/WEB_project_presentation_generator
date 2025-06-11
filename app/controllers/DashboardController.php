<?php

require_once __DIR__ . '/../core/AuthMiddleware.php';

class DashboardController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function index()
    {
        $workspaceModel = $this->model('Workspace');
        $presentationModel = $this->model('Presentation');
        
        $userId = AuthMiddleware::currentUserId();
        $workspaces = $workspaceModel->getUserWorkspaces($userId);
        
        $this->view('dashboard/index', [
            'title' => 'Табло',
            'workspaces' => $workspaces
        ]);
    }

    public function createWorkspace()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $workspaceModel = $this->model('Workspace');
            $name = trim($_POST['name']);
            $userId = AuthMiddleware::currentUserId();

            if ($workspaceModel->create($name, $userId)) {
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $error = 'Възникна грешка при създаването на работното пространство.';
                $this->view('dashboard/create_workspace', ['error' => $error]);
                return;
            }
        }

        $this->view('dashboard/create_workspace');
    }

    public function viewWorkspace($id)
    {
        $workspaceModel = $this->model('Workspace');
        $presentationModel = $this->model('Presentation');
        
        $userId = AuthMiddleware::currentUserId();
        $workspace = $workspaceModel->getById($id);
        
        if (!$workspace || !$workspaceModel->hasAccess($userId, $id)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $presentations = $presentationModel->getByWorkspaceId($id);
        
        $this->view('dashboard/workspace', [
            'title' => $workspace['name'],
            'workspace' => $workspace,
            'presentations' => $presentations,
            'isOwner' => $workspaceModel->isOwner($userId, $id)
        ]);
    }

    public function editWorkspace($id)
    {
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        if (!$workspaceModel->isOwner($userId, $id)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            
            if ($workspaceModel->update($id, $name)) {
                header('Location: ' . BASE_URL . '/dashboard/viewWorkspace/' . $id);
                exit;
            } else {
                $error = 'Възникна грешка при редактирането на работното пространство.';
                $workspace = $workspaceModel->getById($id);
                $this->view('dashboard/edit_workspace', [
                    'title' => 'Редактиране на работно пространство',
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($id);
        $this->view('dashboard/edit_workspace', [
            'title' => 'Редактиране на работно пространство',
            'workspace' => $workspace
        ]);
    }

    public function deleteWorkspace($id)
    {
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        if (!$workspaceModel->isOwner($userId, $id)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($workspaceModel->delete($id)) {
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $error = 'Възникна грешка при изтриването на работното пространство.';
                $workspace = $workspaceModel->getById($id);
                $this->view('dashboard/delete_workspace', [
                    'title' => 'Изтриване на работно пространство',
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($id);
        $this->view('dashboard/delete_workspace', [
            'title' => 'Изтриване на работно пространство',
            'workspace' => $workspace
        ]);
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function shareWorkspace($id)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = $this->model('Workspace');
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $id)) {
            $this->redirect('dashboard', ['error' => 'Нямате права за споделяне на това работно пространство']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $role = 'viewer';

            $result = $workspaceModel->shareWorkspace($id, $email, $role);
            
            if ($result['success']) {
                $this->redirect('dashboard/workspace/' . $id, ['success' => $result['message']]);
            } else {
                $this->redirect('dashboard/workspace/' . $id, ['error' => $result['message']]);
            }
        }

        $workspace = $workspaceModel->getById($id);
        $members = $workspaceModel->getWorkspaceMembers($id);

        $data = [
            'workspace' => $workspace,
            'members' => $members
        ];

        $this->view('dashboard/share_workspace', $data);
    }

    public function removeMember($workspaceId)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $workspaceModel = $this->model('Workspace');
        
        if (!$workspaceModel->isOwner($_SESSION['user_id'], $workspaceId)) {
            $this->redirect('dashboard/workspace/' . $workspaceId, ['error' => 'Нямате права за премахване на членове']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? '';

            $result = $workspaceModel->removeMember($workspaceId, $userId);
            
            if ($result['success']) {
                $this->redirect('dashboard/shareWorkspace/' . $workspaceId, ['success' => $result['message']]);
            } else {
                $this->redirect('dashboard/shareWorkspace/' . $workspaceId, ['error' => $result['message']]);
            }
        }
    }
} 