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
}