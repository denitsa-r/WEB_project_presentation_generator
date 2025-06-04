<?php

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
            'presentations' => $presentations
        ]);
    }
}