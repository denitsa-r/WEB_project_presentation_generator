<?php
require_once __DIR__ . '/../models/Presentation.php';

class PresentationController {
    private $config;
    private $model;

    public function __construct($config) {
        $this->config = $config;
        $this->model = new Presentation($config);
    }

    public function index($workspaceId) {
        $presentations = $this->model->getByWorkspace($workspaceId);
        require __DIR__ . '/../views/presentation/index.php';
    }

    public function view($id) {
        $presentation = $this->model->getById($id);
        if (!$presentation) {
            http_response_code(404);
            echo "Presentation not found";
            exit;
        }
        require __DIR__ . '/../views/presentation/view.php';
    }

    public function create($workspaceId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'workspace_id' => $workspaceId,
                'title' => $_POST['title'] ?? '',
                'language' => $_POST['language'] ?? 'bg',
                'theme' => $_POST['theme'] ?? 'light',
                'version' => $_POST['version'] ?? '1.0',
                'navigation' => $_POST['navigation'] ?? null,
            ];
            $this->model->create($data);
            header("Location: /presentation/index/$workspaceId");
            exit;
        }
        require __DIR__ . '/../views/presentation/create.php';
    }

    public function edit($id) {
        $presentation = $this->model->getById($id);
        if (!$presentation) {
            http_response_code(404);
            echo "Presentation not found";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'language' => $_POST['language'] ?? 'bg',
                'theme' => $_POST['theme'] ?? 'light',
                'version' => $_POST['version'] ?? '1.0',
                'navigation' => $_POST['navigation'] ?? null,
            ];
            $this->model->update($id, $data);
            header("Location: /presentation/view/$id");
            exit;
        }
        require __DIR__ . '/../views/presentation/edit.php';
    }

    public function delete($id) {
        $presentation = $this->model->getById($id);
        if ($presentation) {
            $workspaceId = $presentation['workspace_id'];
            $this->model->delete($id);
            header("Location: /presentation/index/$workspaceId");
            exit;
        } else {
            http_response_code(404);
            echo "Presentation not found";
        }
    }
}