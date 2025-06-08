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

    public function updateSlideOrder()
    {
        // Изключваме извеждането на грешки
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Изчистваме буфера за да сме сигурни, че няма изведен текст преди JSON
        if (ob_get_length()) ob_clean();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $rawInput = file_get_contents('php://input');
            if (empty($rawInput)) {
                throw new Exception('No input data received');
            }

            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
            
            if (!isset($data['presentationId']) || !isset($data['slideOrder'])) {
                throw new Exception('Missing required data');
            }

            $presentationId = $data['presentationId'];
            $slideOrder = $data['slideOrder'];

            if (!is_array($slideOrder)) {
                throw new Exception('Slide order must be an array');
            }

            $slideModel = new Slide();
            $success = true;
            $errorMessage = '';

            foreach ($slideOrder as $index => $slideId) {
                if (!$slideModel->updateOrder($slideId, $index + 1)) {
                    $success = false;
                    $errorMessage = "Failed to update slide ID: $slideId";
                    error_log("Failed to update slide order. Slide ID: $slideId, New order: " . ($index + 1));
                    break;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Slide order updated successfully' : $errorMessage
            ]);
        } catch (Exception $e) {
            error_log("Error in updateSlideOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Error $e) {
            error_log("PHP Error in updateSlideOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
        
        // Спираме изпълнението след изпращане на JSON
        exit;
    }
} 