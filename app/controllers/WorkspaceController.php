<?php
require_once __DIR__ . '/../models/Workspace.php';

class WorkspaceController extends Controller
{
    private $model;

    public function __construct($config)
    {
        $this->model = $this->model('Workspace', $config);  // Зареждаме модел
    }

    public function index()
    {
        $workspaces = $this->model->getAll();
        $this->view('workspace/index', ['workspaces' => $workspaces]);
    }

    public function view($id)
    {
        $workspace = $this->model->getById($id);
        if (!$workspace) {
            http_response_code(404);
            echo "Workspace not found";
            exit;
        }
        $this->view('workspace/view', ['workspace' => $workspace]);
    }
}