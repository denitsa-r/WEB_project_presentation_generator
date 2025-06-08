<?php

class PresentationController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function create($workspaceId)
    {
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        if (!$workspaceModel->hasAccess($userId, $workspaceId)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $presentationModel = $this->model('Presentation');
            $title = trim($_POST['title']);
            $language = $_POST['language'] ?? 'bg';
            $theme = $_POST['theme'] ?? 'light';

            $presentationId = $presentationModel->create($workspaceId, $title, $language, $theme);
            
            if ($presentationId) {
                header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentationId);
                exit;
            } else {
                $error = 'Възникна грешка при създаването на презентацията.';
                $workspace = $workspaceModel->getById($workspaceId);
                $this->view('presentation/create', [
                    'title' => 'Нова презентация',
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($workspaceId);
        $this->view('presentation/create', [
            'title' => 'Нова презентация',
            'workspace' => $workspace
        ]);
    }

    public function viewPresentation($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $slideModel = $this->model('Slide');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $slides = $slideModel->getByPresentationId($id);
        
        $this->view('presentation/view', [
            'title' => $presentation['title'],
            'presentation' => $presentation,
            'workspace' => $workspace,
            'slides' => $slides,
            'isOwner' => $workspaceModel->isOwner($userId, $presentation['workspace_id'])
        ]);
    }

    public function edit($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $language = $_POST['language'] ?? 'bg';
            $theme = $_POST['theme'] ?? 'light';

            if ($presentationModel->update($id, $title, $language, $theme)) {
                header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
                exit;
            } else {
                $error = 'Възникна грешка при редактирането на презентацията.';
                $workspace = $workspaceModel->getById($presentation['workspace_id']);
                $this->view('presentation/edit', [
                    'title' => 'Редактиране на презентация',
                    'presentation' => $presentation,
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $this->view('presentation/edit', [
            'title' => 'Редактиране на презентация',
            'presentation' => $presentation,
            'workspace' => $workspace
        ]);
    }

    public function delete($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$workspaceModel->isOwner($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($presentationModel->delete($id)) {
                header('Location: ' . BASE_URL . '/dashboard/workspace/' . $presentation['workspace_id']);
                exit;
            } else {
                $error = 'Възникна грешка при изтриването на презентацията.';
                $workspace = $workspaceModel->getById($presentation['workspace_id']);
                $this->view('presentation/delete', [
                    'title' => 'Изтриване на презентация',
                    'presentation' => $presentation,
                    'workspace' => $workspace,
                    'error' => $error
                ]);
                return;
            }
        }

        $workspace = $workspaceModel->getById($presentation['workspace_id']);
        $this->view('presentation/delete', [
            'title' => 'Изтриване на презентация',
            'presentation' => $presentation,
            'workspace' => $workspace
        ]);
    }

    public function review($id)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $slideModel = $this->model('Slide');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $slides = $slideModel->getByPresentationId($id);
        
        $this->view('presentation/review', [
            'title' => $presentation['title'],
            'presentation' => $presentation,
            'slides' => $slides
        ]);
    }

    public function updateSlideOrder()
    {
        // Изключваме извеждането на грешки
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Изчистваме буфера за да сме сигурни, че няма изведен текст преди JSON
        if (ob_get_length()) ob_clean();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $rawInput = file_get_contents('php://input');
            if (empty($rawInput)) {
                throw new Exception('No input data received');
            }

            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
            
            if (!isset($data['presentationId']) || !isset($data['slideOrder'])) {
                throw new Exception('Missing required data');
            }

            $presentationId = $data['presentationId'];
            $slideOrder = $data['slideOrder'];

            if (!is_array($slideOrder)) {
                throw new Exception('Slide order must be an array');
            }

            $slideModel = new Slide();
            $success = true;
            $errorMessage = '';

            foreach ($slideOrder as $index => $slideId) {
                if (!$slideModel->updateOrder($slideId, $index + 1)) {
                    $success = false;
                    $errorMessage = "Failed to update slide ID: $slideId";
                    error_log("Failed to update slide order. Slide ID: $slideId, New order: " . ($index + 1));
                    break;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Slide order updated successfully' : $errorMessage
            ]);
        } catch (Exception $e) {
            error_log("Error in updateSlideOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Error $e) {
            error_log("PHP Error in updateSlideOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
        
        // Спираме изпълнението след изпращане на JSON
        exit;
    }

    public function export($id, $format)
    {
        $presentationModel = $this->model('Presentation');
        $workspaceModel = $this->model('Workspace');
        $slideModel = $this->model('Slide');
        $userId = AuthMiddleware::currentUserId();
        
        $presentation = $presentationModel->getById($id);
        
        if (!$presentation || !$workspaceModel->hasAccess($userId, $presentation['workspace_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $slides = $slideModel->getByPresentationId($id);
        
        switch ($format) {
            case 'html':
                $this->exportHTML($presentation, $slides);
                break;
            case 'xml':
                $this->exportXML($presentation, $slides);
                break;
            case 'slim':
                $this->exportSLIM($presentation, $slides);
                break;
            default:
                header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $id);
                exit;
        }
    }

    private function exportHTML($presentation, $slides)
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $presentation['title'] . '.html"');
        
        $theme = $presentation['theme'];
        $isDark = $theme === 'dark';
        
        $html = '<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($presentation['title']) . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: ' . ($isDark ? '#1a1a1a' : '#f5f5f5') . ';
            color: ' . ($isDark ? '#e0e0e0' : '#333') . ';
        }
        .slide { 
            width: 800px; 
            height: 600px; 
            margin: 20px auto; 
            border: 1px solid ' . ($isDark ? '#404040' : '#ccc') . '; 
            padding: 20px; 
            box-sizing: border-box; 
            background-color: ' . ($isDark ? '#2d2d2d' : 'white') . '; 
            box-shadow: 0 2px 4px rgba(0,0,0,' . ($isDark ? '0.3' : '0.1') . '); 
        }
        .slide-title { 
            font-size: 24px; 
            margin-bottom: 20px; 
            color: ' . ($isDark ? '#fff' : '#333') . '; 
        }
        .slide-content { 
            font-size: 18px; 
            color: ' . ($isDark ? '#e0e0e0' : '#444') . '; 
        }
        .slide-image { 
            max-width: 100%; 
            max-height: 400px; 
            display: block; 
            margin: 10px auto; 
        }
        ul { 
            margin: 10px 0; 
            padding-left: 20px; 
        }
        li { 
            margin: 5px 0; 
            color: ' . ($isDark ? '#e0e0e0' : '#444') . '; 
        }
        .element-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: ' . ($isDark ? '#fff' : '#333') . ';
        }
    </style>
</head>
<body>';

        foreach ($slides as $slide) {
            $html .= '<div class="slide">';
            $html .= '<h2 class="slide-title">' . htmlspecialchars($slide['title']) . '</h2>';
            $html .= '<div class="slide-content">';
            
            foreach ($slide['elements'] as $element) {
                if (!empty($element['title'])) {
                    $html .= '<h3 class="element-title">' . htmlspecialchars($element['title']) . '</h3>';
                }
                
                switch ($element['type']) {
                    case 'text':
                        $html .= '<p>' . nl2br(htmlspecialchars($element['content'])) . '</p>';
                        break;
                    case 'image':
                        $html .= '<img src="' . htmlspecialchars($element['content']) . '" class="slide-image" alt="Slide image">';
                        break;
                    case 'list':
                        $items = explode("\n", $element['content']);
                        $html .= '<ul>';
                        foreach ($items as $item) {
                            if (trim($item) !== '') {
                                $html .= '<li>' . htmlspecialchars($item) . '</li>';
                            }
                        }
                        $html .= '</ul>';
                        break;
                }
            }
            
            $html .= '</div></div>';
        }

        $html .= '</body></html>';
        
        echo $html;
        exit;
    }

    private function exportXML($presentation, $slides)
    {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $presentation['title'] . '.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><presentation></presentation>');
        $xml->addChild('title', htmlspecialchars($presentation['title']));
        $xml->addChild('theme', htmlspecialchars($presentation['theme']));
        
        $slidesNode = $xml->addChild('slides');
        
        foreach ($slides as $slide) {
            $slideNode = $slidesNode->addChild('slide');
            $slideNode->addChild('title', htmlspecialchars($slide['title']));
            $slideNode->addChild('order', $slide['slide_order']);
            
            $elementsNode = $slideNode->addChild('elements');
            foreach ($slide['elements'] as $element) {
                $elementNode = $elementsNode->addChild('element');
                $elementNode->addChild('type', $element['type']);
                $elementNode->addChild('content', htmlspecialchars($element['content']));
            }
        }
        
        echo $xml->asXML();
        exit;
    }

    private function exportSLIM($presentation, $slides)
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $presentation['title'] . '.slim"');
        
        $slim = "presentation\n";
        $slim .= "  title: " . $presentation['title'] . "\n";
        $slim .= "  theme: " . $presentation['theme'] . "\n\n";
        
        foreach ($slides as $slide) {
            $slim .= "slide\n";
            $slim .= "  title: " . $slide['title'] . "\n";
            
            foreach ($slide['elements'] as $element) {
                $slim .= "  element\n";
                $slim .= "    type: " . $element['type'] . "\n";
                
                if (!empty($element['title'])) {
                    $slim .= "    title: " . $element['title'] . "\n";
                }
                
                // Заменяме новите редове с \n и екранираме специалните символи
                $content = str_replace(["\r\n", "\r", "\n"], "\\n", $element['content']);
                $content = str_replace(":", "\\:", $content); // Екранираме двоеточието
                $slim .= "    content: " . $content . "\n";
            }
            $slim .= "\n";
        }
        
        error_log("Generated SLIM content: " . $slim);
        echo $slim;
        exit;
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $userId = AuthMiddleware::currentUserId();
        $workspaceId = $_POST['workspaceId'];
        $format = $_POST['importFormat'];
        
        // Проверка за достъп до работното пространство
        $workspaceModel = $this->model('Workspace');
        if (!$workspaceModel->hasAccess($userId, $workspaceId)) {
            $_SESSION['error'] = 'Нямате достъп до това работно пространство.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!isset($_FILES['importFile']) || $_FILES['importFile']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Грешка при качване на файла.';
            header('Location: ' . BASE_URL . '/presentation/create');
            exit;
        }

        $fileContent = file_get_contents($_FILES['importFile']['tmp_name']);
        if ($fileContent === false) {
            $_SESSION['error'] = 'Грешка при четене на файла.';
            header('Location: ' . BASE_URL . '/presentation/create');
            exit;
        }

        $presentationData = null;
        switch ($format) {
            case 'html':
                $presentationData = $this->parseHTML($fileContent);
                break;
            case 'xml':
                $presentationData = $this->parseXML($fileContent);
                break;
            case 'slim':
                $presentationData = $this->parseSLIM($fileContent);
                break;
            default:
                $_SESSION['error'] = 'Неподдържан формат.';
                header('Location: ' . BASE_URL . '/presentation/create');
                exit;
        }

        if (!$presentationData) {
            $_SESSION['error'] = 'Грешка при обработка на файла.';
            header('Location: ' . BASE_URL . '/presentation/create');
            exit;
        }

        $presentationModel = $this->model('Presentation');
        $slideModel = $this->model('Slide');
        
        // Създаване на презентацията
        $presentationId = $presentationModel->create(
            $workspaceId,
            $presentationData['title'],
            'bg',
            $presentationData['theme'] ?? 'light'
        );

        if (!$presentationId) {
            $_SESSION['error'] = 'Грешка при създаване на презентацията.';
            header('Location: ' . BASE_URL . '/presentation/create');
            exit;
        }

        // Създаване на слайдовете
        foreach ($presentationData['slides'] as $index => $slide) {
            $slideId = $slideModel->create([
                'presentation_id' => $presentationId,
                'title' => $slide['title'],
                'slide_order' => $index,
                'layout' => 'default'
            ]);

            if ($slideId) {
                foreach ($slide['elements'] as $element) {
                    $slideModel->addElement($slideId, $element['type'], $element['content'], $element['title'] ?? null);
                }
            }
        }

        $_SESSION['success'] = 'Презентацията е импортирана успешно.';
        header('Location: ' . BASE_URL . '/presentation/viewPresentation/' . $presentationId);
        exit;
    }

    private function parseHTML($content)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($content, LIBXML_NOERROR);
        
        $title = '';
        $titleElements = $dom->getElementsByTagName('title');
        if ($titleElements->length > 0) {
            $title = $titleElements->item(0)->textContent;
        }

        $slides = [];
        $slideElements = $dom->getElementsByTagName('div');
        foreach ($slideElements as $slideElement) {
            if ($slideElement->getAttribute('class') === 'slide') {
                $slide = [
                    'title' => '',
                    'elements' => []
                ];

                $titleElements = $slideElement->getElementsByTagName('h2');
                if ($titleElements->length > 0) {
                    $slide['title'] = $titleElements->item(0)->textContent;
                }

                $contentElement = $slideElement->getElementsByTagName('div')->item(0);
                if ($contentElement) {
                    foreach ($contentElement->childNodes as $node) {
                        if ($node->nodeType === XML_ELEMENT_NODE) {
                            switch ($node->nodeName) {
                                case 'p':
                                    $slide['elements'][] = [
                                        'type' => 'text',
                                        'content' => $node->textContent
                                    ];
                                    break;
                                case 'img':
                                    $slide['elements'][] = [
                                        'type' => 'image',
                                        'content' => $node->getAttribute('src')
                                    ];
                                    break;
                                case 'ul':
                                    $items = [];
                                    foreach ($node->getElementsByTagName('li') as $li) {
                                        $items[] = $li->textContent;
                                    }
                                    $slide['elements'][] = [
                                        'type' => 'list',
                                        'content' => implode("\n", $items)
                                    ];
                                    break;
                                case 'h3':
                                    if (!empty($slide['elements'])) {
                                        $slide['elements'][count($slide['elements']) - 1]['title'] = $node->textContent;
                                    }
                                    break;
                            }
                        }
                    }
                }

                $slides[] = $slide;
            }
        }

        return [
            'title' => $title,
            'slides' => $slides
        ];
    }

    private function parseXML($content)
    {
        $xml = simplexml_load_string($content);
        if (!$xml) {
            return null;
        }

        $slides = [];
        foreach ($xml->slides->slide as $slide) {
            $slideData = [
                'title' => (string)$slide->title,
                'elements' => []
            ];

            foreach ($slide->elements->element as $element) {
                $elementData = [
                    'type' => (string)$element->type,
                    'content' => (string)$element->content
                ];
                $slideData['elements'][] = $elementData;
            }

            $slides[] = $slideData;
        }

        return [
            'title' => (string)$xml->title,
            'theme' => (string)$xml->theme,
            'slides' => $slides
        ];
    }

    private function parseSLIM($content)
    {
        error_log("Starting SLIM parsing with content: " . $content);
        
        $lines = explode("\n", $content);
        $currentSlide = null;
        $currentElement = null;
        $slides = [];
        $title = '';
        $theme = 'light';

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            error_log("Processing line " . ($lineNumber + 1) . ": " . $line);

            if (strpos($line, 'presentation') === 0) {
                error_log("Found presentation start");
                continue;
            } elseif (strpos($line, '  title:') === 0) {
                $title = trim(substr($line, 8));
                error_log("Found presentation title: " . $title);
            } elseif (strpos($line, '  theme:') === 0) {
                $theme = trim(substr($line, 8));
                error_log("Found theme: " . $theme);
            } elseif (strpos($line, 'slide') === 0) {
                if ($currentSlide) {
                    error_log("Adding slide to slides array: " . print_r($currentSlide, true));
                    $slides[] = $currentSlide;
                }
                $currentSlide = [
                    'title' => '',
                    'elements' => []
                ];
                error_log("Created new slide");
            } elseif (strpos($line, '  title:') === 0 && $currentSlide) {
                $currentSlide['title'] = trim(substr($line, 8));
                error_log("Set slide title: " . $currentSlide['title']);
            } elseif (strpos($line, '  element') === 0) {
                if ($currentSlide) {
                    $currentElement = [
                        'type' => '',
                        'content' => '',
                        'title' => null
                    ];
                    $currentSlide['elements'][] = &$currentElement;
                    error_log("Created new element");
                }
            } elseif (strpos($line, '    type:') === 0 && $currentElement) {
                $currentElement['type'] = trim(substr($line, 9));
                error_log("Set element type: " . $currentElement['type']);
            } elseif (strpos($line, '    content:') === 0 && $currentElement) {
                // Премахваме екранираните двоеточия и нови редове
                $content = trim(substr($line, 11));
                $content = str_replace('\\:', ':', $content);
                $content = str_replace('\\n', "\n", $content);
                $currentElement['content'] = $content;
                error_log("Set element content: " . $currentElement['content']);
            } elseif (strpos($line, '    title:') === 0 && $currentElement) {
                $currentElement['title'] = trim(substr($line, 9));
                error_log("Set element title: " . $currentElement['title']);
            }
        }

        if ($currentSlide) {
            error_log("Adding final slide to slides array: " . print_r($currentSlide, true));
            $slides[] = $currentSlide;
        }

        $result = [
            'title' => $title,
            'theme' => $theme,
            'slides' => $slides
        ];

        error_log("Final parsed result: " . print_r($result, true));
        return $result;
    }
} 