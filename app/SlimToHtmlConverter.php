<?php

use Slim\Views\Twig;
use Slim\Factory\AppFactory;

class SlimToHtmlConverter
{
    private $twig;

    public function __construct()
    {
        $templatesPath = __DIR__ . '/../views/slim_templates'; 

        $this->twig = new Twig($templatesPath);
    }

    public function convert($templateFile, $data = [])
    {
        try {
            return $this->twig->render($templateFile, $data);
        } catch (Exception $e) {
            echo 'Error rendering Slim template: ' . $e->getMessage();
            return '';
        }
    }
}