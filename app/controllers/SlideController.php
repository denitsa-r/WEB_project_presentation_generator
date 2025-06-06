<?php

require_once __DIR__ . '/../models/Slide.php';
require_once __DIR__ . '/../models/Presentation.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';

class SlideController extends Controller
{
    private $slideModel;
    private $presentationModel;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->slideModel = new Slide();
        $this->presentationModel = new Presentation();
    }

    public function create($presentationId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if this is a cancel action
            if (isset($_POST['cancel'])) {
                $presentation_id = $_POST['presentation_id'] ?? null;
                if ($presentation_id) {
                    header("Location: " . BASE_URL . "/presentation/viewPresentation/" . $presentation_id);
                    exit;
                } else {
                    header("Location: " . BASE_URL . "/presentations");
                    exit;
                }
            }

            $presentation_id = $_POST['presentation_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $layout = $_POST['layout'] ?? 'full';
            
            // Collect content from all elements
            $content = [];
            $index = 0;
            while (isset($_POST["content_type_$index"])) {
                $type = $_POST["content_type_$index"];
                $elementTitle = $_POST["content_title_$index"] ?? '';
                $elementContent = $_POST["content_content_$index"] ?? '';
                $elementText = $_POST["content_text_$index"] ?? '';
                
                $content[] = [
                    'type' => $type,
                    'title' => $elementTitle,
                    'content' => $elementContent,
                    'text' => $elementText
                ];
                
                $index++;
            }
            
            if (empty($title)) {
                $_SESSION['error'] = 'Заглавието е задължително';
                header("Location: " . BASE_URL . "/slides/create?presentation_id=" . $presentation_id);
                exit;
            }
            
            try {
                $slide = new Slide();
                $slide->create([
                    'presentation_id' => $presentation_id,
                    'title' => $title,
                    'layout' => $layout,
                    'content' => json_encode($content)
                ]);
                
                $_SESSION['success'] = 'Слайдът е създаден успешно';
                header("Location: " . BASE_URL . "/presentation/viewPresentation/" . $presentation_id);
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Грешка при създаване на слайда: ' . $e->getMessage();
                header("Location: " . BASE_URL . "/slides/create?presentation_id=" . $presentation_id);
                exit;
            }
        } else {
            // GET request - show create form
            if (!$presentationId) {
                $_SESSION['error'] = 'Не е посочена презентация';
                header("Location: " . BASE_URL . "/presentations");
                exit;
            }

            $presentation = $this->presentationModel->getById($presentationId);
            if (!$presentation) {
                $_SESSION['error'] = 'Презентацията не е намерена';
                header("Location: " . BASE_URL . "/presentations");
                exit;
            }

            $this->view('slides/create', [
                'presentation' => $presentation
            ]);
        }
    }

    public function edit($slideId)
    {
        $slideModel = $this->model('Slide');
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $slide = $slideModel->getById($slideId);
        
        if (!$slide) {
            header('Location: ' . BASE_URL . '/presentation');
            exit;
        }

        $presentation = $presentationModel->getById($slide['presentation_id']);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentation['id']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $presentation_id = $_POST['presentation_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $layout = $_POST['layout'] ?? 'full';
            
            // Collect content from all elements
            $content = [];
            $index = 0;
            while (isset($_POST["content_type_$index"])) {
                $type = $_POST["content_type_$index"];
                $elementTitle = $_POST["content_title_$index"] ?? '';
                $elementContent = $_POST["content_content_$index"] ?? '';
                $elementText = $_POST["content_text_$index"] ?? '';
                
                $content[] = [
                    'type' => $type,
                    'title' => $elementTitle,
                    'content' => $elementContent,
                    'text' => $elementText
                ];
                
                $index++;
            }
            
            if (empty($title)) {
                $_SESSION['error'] = 'Заглавието е задължително';
                header("Location: /slides/edit?id=" . $id);
                exit;
            }
            
            try {
                $slide = new Slide();
                $slide->update($id, [
                    'title' => $title,
                    'layout' => $layout,
                    'content' => json_encode($content)
                ]);
                
                $_SESSION['success'] = 'Слайдът е редактиран успешно';
                header("Location: /presentations/view?id=" . $presentation_id);
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Грешка при редактиране на слайда: ' . $e->getMessage();
                header("Location: /slides/edit?id=" . $id);
                exit;
            }
        } else {
            $this->view('slides/edit', [
                'slide' => $slide
            ]);
        }
    }

    public function delete($id)
    {
        $slideModel = $this->model('Slide');
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $slide = $slideModel->getById($id);
        
        if (!$slide) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $presentation = $presentationModel->getById($slide['presentation_id']);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentation['id']);
            exit;
        }

        if ($slideModel->delete($id)) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentation['id']);
            exit;
        } else {
            $error = 'Възникна грешка при изтриването на слайда.';
            $this->view('slides/delete', [
                'title' => 'Изтриване на слайд',
                'slide' => $slide,
                'presentation' => $presentation,
                'error' => $error
            ]);
        }
    }
} 