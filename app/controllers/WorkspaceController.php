<?php
require_once __DIR__ . '/../models/Workspace.php';

class WorkspaceController {
    private $config;
    private $model;

    public function __construct($config) {
        $this->config = $config;
        $this->model = new Workspace($config);
    }

    public function index() {
        $workspaces = $this->model->getAll();
        require __DIR__ . '/../views/workspace/index.php';
    }

    public function view($id) {
        $workspace = $this->model->getById($id);
        if (!$workspace) {
            http_response_code(404);
            echo "Workspace not found";
            exit;
        }
        require __DIR__ . '/../views/workspace/view.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'language' => $_POST['language'] ?? ''
            ];
            $this->model->create($data);
            header('Location: /workspace/index');
            exit;
        }
        require __DIR__ . '/../views/workspace/create.php';
    }

    public function edit($id) {
        $workspace = $this->model->getById($id);
        if (!$workspace) {
            http_response_code(404);
            echo "Workspace not found";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'language' => $_POST['language'] ?? ''
            ];
            $this->model->update($id, $data);
            header('Location: /workspace/index');
            exit;
        }

        require __DIR__ . '/../views/workspace/edit.php';
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: /workspace/index');
        exit;
    }
}