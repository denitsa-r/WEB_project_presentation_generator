<?php
require_once __DIR__ . '/../models/Slide.php';

class SlideController {
    private $config;
    private $model;

    public function __construct($config) {
        $this->config = $config;
        $this->model = new Slide($config);
    }

    public function index($presentationId) {
        $slides = $this->model->getByPresentation($presentationId);
        require __DIR__ . '/../views/slide/index.php';
    }

    public function view($id) {
        $slide = $this->model->getById($id);
        if (!$slide) {
            http_response_code(404);
            echo "Slide not found";
            exit;
        }
        require __DIR__ . '/../views/slide/view.php';
    }
}