<?php

class PresentationController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function create($workspaceId)
    {
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        if (!$workspaceModel->hasAccess($userId, $workspaceId)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $presentationModel = $this->model('Presentation');
            $title = trim($_POST['title']);
            $language = $_POST['language'] ?? 'bg';
            $theme = $_POST['theme'] ?? 'light';

            $presentationId = $presentationModel->create($workspaceId, $title, $language, $theme);
            
            if ($presentationId) {
                header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentationId);
                exit;
            } else {
                $error = 'Възникна грешка при създаването на презентацията.';
                $workspace = $workspaceModel->getById($workspaceId);
                $this->view('presentation/create', [
                    'title' => 'Нова презентация',
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($workspaceId);
        $this->view('presentation/create', [
            'title' => 'Нова презентация',
            'workspace' => $workspace
        ]);
    }

    public function viewPresentation($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $slideModel = $this->model('Slide');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $slides = $slideModel->getByPresentationId($id);
        
        $this->view('presentation/view', [
            'title' => $presentation['title'],
            'presentation' => $presentation,
            'workspace' => $workspace,
            'slides' => $slides,
            'isOwner' => $workspaceModel->isOwner($userId, $presentation['workspace_id'])
        ]);
    }

    public function edit($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $language = $_POST['language'] ?? 'bg';
            $theme = $_POST['theme'] ?? 'light';

            if ($presentationModel->update($id, $title, $language, $theme)) {
                header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
                exit;
            } else {
                $error = 'Възникна грешка при редактирането на презентацията.';
                $workspace = $workspaceModel->getById($presentation['workspace_id']);
                $this->view('presentation/edit', [
                    'title' => 'Редактиране на презентация',
                    'presentation' => $presentation,
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $this->view('presentation/edit', [
            'title' => 'Редактиране на презентация',
            'presentation' => $presentation,
            'workspace' => $workspace
        ]);
    }

    public function delete($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($presentationModel->delete($id)) {
                header('Location: ' . BASE_URL . '/dashboard/workspace/' . $presentation['workspace_id']);
                exit;
            } else {
                $error = 'Възникна грешка при изтриването на презентацията.';
                $workspace = $workspaceModel->getById($presentation['workspace_id']);
                $this->view('presentation/delete', [
                    'title' => 'Изтриване на презентация',
                    'presentation' => $presentation,
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $this->view('presentation/delete', [
            'title' => 'Изтриване на презентация',
            'presentation' => $presentation,
            'workspace' => $workspace
        ]);
    }

    public function review($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $slideModel = $this->model('Slide');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $slides = $slideModel->getByPresentationId($id);
        
        $this->view('presentation/review', [
            'title' => $presentation['title'],
            'presentation' => $presentation,
            'slides' => $slides
        ]);
    }
} 