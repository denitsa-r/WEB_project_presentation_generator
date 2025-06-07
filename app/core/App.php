<?php
require_once __DIR__ . '/../helpers/Logger.php';

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        error_log('[App] REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
        $url = $this->parseUrl();
        error_log('[App] Парсиран URL: ' . print_r($url, true));
        Logger::log("App::__construct - REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        Logger::log("App::__construct - REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        Logger::log("App::__construct - SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
        Logger::log("App::__construct - PHP_SELF: " . $_SERVER['PHP_SELF']);

        // Контролер
        if (isset($url[0])) {
            // Премахваме множествено число от името на контролера
            $controllerName = rtrim($url[0], 's');
            if (file_exists("../app/controllers/" . ucfirst($controllerName) . "Controller.php")) {
                $this->controller = ucfirst($controllerName) . 'Controller';
            unset($url[0]);
                Logger::log("App::__construct - Selected controller: " . $this->controller);
            }
        }

        require_once "../app/controllers/{$this->controller}.php";
        $this->controller = new $this->controller;

        // Метод
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
                Logger::log("App::__construct - Selected method: " . $this->method);
            } else {
                // Ако методът не съществува, пренасочваме към dashboard
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            }
        }

        // Параметри
        $this->params = $url ? array_values($url) : [];
        Logger::log("App::__construct - Parameters: " . print_r($this->params, true));

        // Изпълнение
        Logger::log("App::__construct - Calling controller method with parameters");
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    protected function parseUrl()
    {
        // Извличаме URL от REQUEST_URI
        $requestUri = $_SERVER['REQUEST_URI'];
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        
        // Премахваме базовия път от REQUEST_URI
        if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Премахваме query string ако има такъв
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        // Разделяме URL на сегменти
        $url = explode('/', trim($requestUri, '/'));
        Logger::log("App::parseUrl - Parsed URL from REQUEST_URI: " . print_r($url, true));
        
        // Ако няма URL сегменти, връщаме ['home']
        if (empty($url[0])) {
        return ['home'];
        }
        
        // Проверяваме дали първият сегмент е 'public'
        if ($url[0] === 'public') {
            array_shift($url);
        }
        
        // Проверяваме дали първият сегмент е 'web-project'
        if ($url[0] === 'web-project') {
            array_shift($url);
        }
        
        // Проверяваме дали първият сегмент е 'WEB_project_presentation_generator'
        if ($url[0] === 'WEB_project_presentation_generator') {
            array_shift($url);
        }
        
        Logger::log("App::parseUrl - Final URL segments: " . print_r($url, true));
        return $url;
    }
}
