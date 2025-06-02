<?php
class HomeController {
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function index() {
        $workspaces = ['Workspace 1', 'Workspace 2'];

        require __DIR__ . '/../views/home.php';
    }
}