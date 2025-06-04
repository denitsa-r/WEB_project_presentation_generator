<?php
class Controller
{
    public function view($view, $data = [])
    {
        extract($data);  

        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "View not found: $view";
            exit;
        }
    }

    public function model($model)
    {
        $modelPath = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            echo "Model not found: $model";
            exit;
        }
    }
}