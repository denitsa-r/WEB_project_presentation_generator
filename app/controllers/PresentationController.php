<?php
class PresentationController extends Controller
{
    private $model;

    public function __construct($config)
    {
        $this->model = $this->model('Presentation', $config);  // Зареждаме модел
    }

    public function index($workspaceId)
    {
        $presentations = $this->model->getByWorkspace($workspaceId);
        $this->view('presentation/index', ['presentations' => $presentations]);
    }

    public function view($id)
    {
        $presentation = $this->model->getById($id);
        if (!$presentation) {
            http_response_code(404);
            echo "Presentation not found";
            exit;
        }
        $this->view('presentation/view', ['presentation' => $presentation]);
    }
}