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

    public function create($presentationId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'presentation_id' => $presentationId,
                'slide_order' => $_POST['slide_order'] ?? 1,
                'type' => $_POST['type'] ?? 'title_text',
                'layout' => $_POST['layout'] ?? null,
                'style' => $_POST['style'] ?? 'light',
                'content' => $_POST['content'] ?? '',
                'navigation' => $_POST['navigation'] ?? null,
            ];
            $this->model->create($data);
            header("Location: /slide/index/$presentationId");
            exit;
        }
        require __DIR__ . '/../views/slide/create.php';
    }

    public function edit($id) {
        $slide = $this->model->getById($id);
        if (!$slide) {
            http_response_code(404);
            echo "Slide not found";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'slide_order' => $_POST['slide_order'] ?? 1,
                'type' => $_POST['type'] ?? 'title_text',
                'layout' => $_POST['layout'] ?? null,
                'style' => $_POST['style'] ?? 'light',
                'content' => $_POST['content'] ?? '',
                'navigation' => $_POST['navigation'] ?? null,
            ];
            $this->model->update($id, $data);
            header("Location: /slide/view/$id");
            exit;
        }
        require __DIR__ . '/../views/slide/edit.php';
    }

    public function delete($id) {
        $slide = $this->model->getById($id);
        if ($slide) {
            $presentationId = $slide['presentation_id'];
            $this->model->delete($id);
            header("Location: /slide/index/$presentationId");
            exit;
        } else {
            http_response_code(404);
            echo "Slide not found";
        }
    }
}