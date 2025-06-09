<?php

class PresentationController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function create($workspaceId = null)
    {
        if ($workspaceId === null) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

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
        error_log("Exporting presentation: " . print_r($presentation, true));
        error_log("Exporting slides: " . print_r($slides, true));
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . htmlspecialchars($presentation['title']) . '.html"');
        
        $theme = $presentation['theme'] ?? 'light';
        
        $html = '<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($presentation['title']) . '</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .presentation-view {
            max-width: 1200px;
            margin: 0 auto;
        }
        .slide {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
        }
        .slide-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .content-element {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .content-element h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .content-element p {
            margin: 0;
        }
        .content-element.type-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .content-element.type-quote blockquote {
            margin: 0;
            padding-left: 15px;
            border-left: 4px solid #ddd;
            font-style: italic;
        }
        .content-element.type-quote cite {
            display: block;
            margin-top: 10px;
            font-style: normal;
        }
        .content-element.type-image .image-container {
            width: 100%;
            height: 300px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content-element.type-image_text {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .content-element.type-image_text .image-container {
            flex: 1;
            height: 300px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content-element.type-image_text .text {
            flex: 1;
        }
        .content-element.type-image_list {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .content-element.type-image_list .image-container {
            flex: 1;
            height: 300px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content-element.type-image_list ul {
            flex: 1;
        }

        /* Layout styles */
        .slide-content.full {
            width: 100%;
        }
        
        .slide-content.two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .slide-content.two-rows {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .slide-content.three-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .slide-content.three-rows {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .slide-content.grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .slide-content.grid-2x3 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .slide-content.grid-2x4 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 2rem;
        }
        
        .slide-content.grid-3x2 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .slide-content.grid-3x3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .slide-content.grid-4x2 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 2rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .slide-content.two-columns,
            .slide-content.three-columns,
            .slide-content.grid-2x2,
            .slide-content.grid-2x3,
            .slide-content.grid-2x4,
            .slide-content.grid-3x2,
            .slide-content.grid-3x3,
            .slide-content.grid-4x2 {
                grid-template-columns: 1fr;
            }

            .content-element.type-image_text .image-text-container,
            .content-element.type-image_list .image-list-container {
                flex-direction: column;
            }

            .content-element.type-image_text .image-container,
            .content-element.type-image_list .image-container {
                height: 200px;
            }
        }

        .presentation-view[data-theme="dark"] .slide {
            background-color: #2d2d2d;
            color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .presentation-view[data-theme="dark"] .slide-title {
            color: #ffffff;
        }
        .presentation-view[data-theme="dark"] .content-element {
            background-color: #333333;
            border-color: #404040;
        }
        .presentation-view[data-theme="dark"] .content-element h3 {
            color: #ffffff;
        }
        .presentation-view[data-theme="dark"] .content-element p {
            color: #e0e0e0;
        }
        .presentation-view[data-theme="dark"] .content-element.type-list li {
            color: #e0e0e0;
        }
        .presentation-view[data-theme="dark"] .content-element.type-quote blockquote {
            border-left-color: #666666;
            color: #e0e0e0;
        }
        .presentation-view[data-theme="dark"] .content-element.type-quote cite {
            color: #b3b3b3;
        }

        .presentation-view[data-theme="barbie"] .slide {
            background-color: #ffc3f0;
            color: #FD269B;
            box-shadow: 0 2px 4px rgba(253, 38, 155, 0.3);
        }
        .presentation-view[data-theme="barbie"] .slide-title {
            color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element {
            background-color: #ffc3f0;
            border-color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element h3 {
            color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element p {
            color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element.type-list li {
            color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element.type-quote blockquote {
            border-left-color: #FBB659;
            color: #FD269B;
        }
        .presentation-view[data-theme="barbie"] .content-element.type-quote cite {
            color: #FBB659;
        }

        .presentation-view[data-theme="ken"] .slide {
            background-color: #B4E6FF;
            color: #1963ad;
            box-shadow: 0 2px 4px rgba(58, 142, 228, 0.3);
        }
        .presentation-view[data-theme="ken"] .slide-title {
            color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element {
            background-color: #B4E6FF;
            border-color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element h3 {
            color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element p {
            color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element.type-list li {
            color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element.type-quote blockquote {
            border-left-color: #ffa5be;
            color: #1963ad;
        }
        .presentation-view[data-theme="ken"] .content-element.type-quote cite {
            color: #ffa5be;
        }
    </style>
</head>
<body>
    <div class="presentation-view" data-theme="' . htmlspecialchars($theme) . '">';

        foreach ($slides as $slide) {
            error_log("Processing slide: " . print_r($slide, true));
            
            $slideTitle = $slide['title'] ?? '';
            $slideLayout = $slide['layout'] ?? 'full';
            
            $html .= '<div class="slide">
                <h2 class="slide-title">' . htmlspecialchars($slideTitle) . '</h2>
                <div class="slide-content ' . htmlspecialchars($slideLayout) . '">';

            if (!empty($slide['elements'])) {
                error_log("Slide has elements: " . print_r($slide['elements'], true));
                
                foreach ($slide['elements'] as $element) {
                    error_log("Processing element: " . print_r($element, true));
                    
                    $elementType = $element['type'] ?? '';
                    $elementTitle = $element['title'] ?? '';
                    $elementContent = $element['content'] ?? '';
                    $elementText = $element['text'] ?? '';
                    
                    error_log("Element data - Type: $elementType, Title: $elementTitle, Content: $elementContent, Text: $elementText");
                    
                    $html .= '<div class="element-container">';
                    
                    if (!empty($elementTitle)) {
                        $html .= '<h3 class="element-title">' . htmlspecialchars($elementTitle) . '</h3>';
                    }

                    switch ($elementType) {
                        case 'image':
                            $html .= '<div class="content-element type-image">
                                <div class="image-container" style="background-image: url(\'' . htmlspecialchars($elementContent) . '\');"></div>
                            </div>';
                            break;
                        
                        case 'image_text':
                            $html .= '<div class="content-element type-image_text">
                                <div class="image-text-container">
                                    <div class="image-container" style="background-image: url(\'' . htmlspecialchars($elementContent) . '\');"></div>
                                    <div class="text"><p>' . nl2br(htmlspecialchars($elementText)) . '</p></div>
                                </div>
                            </div>';
                            break;
                        
                        case 'image_list':
                            $html .= '<div class="content-element type-image_list">
                                <div class="image-list-container">
                                    <div class="image-container" style="background-image: url(\'' . htmlspecialchars($elementContent) . '\');"></div>
                                    <ul>';
                            foreach (explode("\n", $elementText) as $item) {
                                if (trim($item) !== '') {
                                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                                }
                            }
                            $html .= '</ul></div></div>';
                            break;
                        
                        case 'quote':
                            $html .= '<div class="content-element type-quote">
                                <blockquote>' . nl2br(htmlspecialchars($elementContent));
                            if (!empty($elementTitle)) {
                                $html .= '<cite>— ' . htmlspecialchars($elementTitle) . '</cite>';
                            }
                            $html .= '</blockquote></div>';
                            break;
                        
                        case 'list':
                            $html .= '<div class="content-element type-list"><ul>';
                            foreach (explode("\n", $elementContent) as $item) {
                                if (trim($item) !== '') {
                                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                                }
                            }
                            $html .= '</ul></div>';
                            break;
                        
                        case 'text':
                        default:
                            $html .= '<div class="content-element type-text">' . 
                                nl2br(htmlspecialchars($elementContent)) . 
                            '</div>';
                            break;
                    }
                    
                    $html .= '</div>';
                }
            } else {
                $html .= '<div class="empty-content">Няма добавено съдържание</div>';
            }

            $html .= '</div></div>';
        }

        $html .= '</div></body></html>';
        
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
                
                // Екранираме специалните символи
                $content = $element['content'];
                $content = str_replace(":", "\\:", $content); // Екранираме двоеточието
                $content = str_replace("\n", "\\n", $content); // Екранираме новите редове
                $content = str_replace("\r", "\\r", $content); // Екранираме carriage return
                $content = str_replace("\t", "\\t", $content); // Екранираме табулации
                
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
        error_log("Starting import process");
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $userId = AuthMiddleware::currentUserId();
        
        if (!isset($_POST['workspaceId'])) {
            error_log("Missing workspaceId in POST data");
            $_SESSION['error'] = 'Липсващо ID на работно пространство.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        if (!isset($_POST['importFormat'])) {
            error_log("Missing importFormat in POST data");
            $_SESSION['error'] = 'Липсващ формат на файла.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        $workspaceId = $_POST['workspaceId'];
        $format = $_POST['importFormat'];
        
        error_log("Import parameters - workspaceId: $workspaceId, format: $format");
        
        // Проверка за достъп до работното пространство
        $workspaceModel = $this->model('Workspace');
        if (!$workspaceModel->hasAccess($userId, $workspaceId)) {
            error_log("User $userId does not have access to workspace $workspaceId");
            $_SESSION['error'] = 'Нямате достъп до това работно пространство.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!isset($_FILES['importFile']) || $_FILES['importFile']['error'] !== UPLOAD_ERR_OK) {
            error_log("File upload error: " . ($_FILES['importFile']['error'] ?? 'No file uploaded'));
            $_SESSION['error'] = 'Грешка при качване на файла.';
            header('Location: ' . BASE_URL . '/presentation/create/' . $workspaceId);
            exit;
        }

        $fileContent = file_get_contents($_FILES['importFile']['tmp_name']);
        if ($fileContent === false) {
            error_log("Failed to read file contents");
            $_SESSION['error'] = 'Грешка при четене на файла.';
            header('Location: ' . BASE_URL . '/presentation/create/' . $workspaceId);
            exit;
        }

        error_log("File content length: " . strlen($fileContent));
        
        $presentationData = null;
        switch ($format) {
            case 'html':
                error_log("Parsing HTML format");
                $presentationData = $this->parseHTML($fileContent);
                break;
            case 'xml':
                error_log("Parsing XML format");
                $presentationData = $this->parseXML($fileContent);
                break;
            case 'slim':
                error_log("Parsing SLIM format");
                $presentationData = $this->parseSLIM($fileContent);
                break;
            default:
                error_log("Unsupported format: $format");
                $_SESSION['error'] = 'Неподдържан формат.';
                header('Location: ' . BASE_URL . '/presentation/create/' . $workspaceId);
                exit;
        }

        if (!$presentationData) {
            error_log("Failed to parse file data");
            $_SESSION['error'] = 'Грешка при обработка на файла.';
            header('Location: ' . BASE_URL . '/presentation/create/' . $workspaceId);
            exit;
        }

        error_log("Parsed presentation data: " . print_r($presentationData, true));

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
            error_log("Failed to create presentation");
            $_SESSION['error'] = 'Грешка при създаване на презентацията.';
            header('Location: ' . BASE_URL . '/presentation/create/' . $workspaceId);
            exit;
        }

        error_log("Created presentation with ID: $presentationId");

        // Създаване на слайдовете
        foreach ($presentationData['slides'] as $index => $slide) {
            error_log("Creating slide $index: " . print_r($slide, true));
            
            $slideId = $slideModel->create([
                'presentation_id' => $presentationId,
                'title' => $slide['title'],
                'slide_order' => $index,
                'layout' => $slide['layout'] ?? 'full'
            ]);

            if ($slideId) {
                error_log("Created slide with ID: $slideId");
                foreach ($slide['elements'] as $element) {
                    error_log("Adding element to slide: " . print_r($element, true));
                    $slideModel->addElement($slideId, $element['type'], $element['content'], $element['title'] ?? null);
                }
            } else {
                error_log("Failed to create slide");
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
        
        // Извличане на темата
        $theme = 'light';
        $presentationView = $dom->getElementsByTagName('div')->item(0);
        if ($presentationView && $presentationView->getAttribute('class') === 'presentation-view') {
            $theme = $presentationView->getAttribute('data-theme') ?? 'light';
        }
        
        // Извличане на заглавието
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
                    'layout' => 'full',
                    'elements' => []
                ];

                // Извличане на заглавието на слайда
                $titleElements = $slideElement->getElementsByTagName('h2');
                if ($titleElements->length > 0) {
                    $slide['title'] = $titleElements->item(0)->textContent;
                }

                // Извличане на layout-а
                $contentElements = $slideElement->getElementsByTagName('div');
                foreach ($contentElements as $contentElement) {
                    $classes = explode(' ', $contentElement->getAttribute('class'));
                    foreach ($classes as $class) {
                        if ($class === 'slide-content') {
                            // Намираме следващия клас, който е layout-ът
                            foreach ($classes as $layoutClass) {
                                if ($layoutClass !== 'slide-content' && !empty($layoutClass)) {
                                    $slide['layout'] = $layoutClass;
                                    break 2;
                                }
                            }
                        }
                    }
                }

                // Обработка на елементите
                foreach ($contentElements as $contentElement) {
                    if ($contentElement->getAttribute('class') === 'element-container') {
                        $element = $this->parseElement($contentElement);
                        if ($element) {
                            $slide['elements'][] = $element;
                        }
                    }
                }

                $slides[] = $slide;
            }
        }

        return [
            'title' => $title,
            'theme' => $theme,
            'slides' => $slides
        ];
    }

    private function parseElement($contentElement)
    {
        $element = [
            'type' => '',
            'content' => '',
            'title' => '',
            'text' => '',
            'style' => '{}'
        ];

        // Извличане на заглавието
        $titleElement = $contentElement->getElementsByTagName('h3')->item(0);
        if ($titleElement) {
            $element['title'] = $titleElement->textContent;
        }

        // Извличане на типа на елемента
        $contentElementDiv = $contentElement->getElementsByTagName('div')->item(0);
        if ($contentElementDiv) {
            $classes = explode(' ', $contentElementDiv->getAttribute('class'));
            foreach ($classes as $class) {
                if (strpos($class, 'type-') === 0) {
                    $element['type'] = substr($class, 5);
                    break;
                }
            }
        }

        // Извличане на стиловете
        if ($contentElementDiv) {
            $style = $contentElementDiv->getAttribute('style');
            if ($style) {
                $element['style'] = json_encode(['style' => $style]);
            }
        }

        // Обработка според типа на елемента
        switch ($element['type']) {
            case 'image':
                $imageContainer = $contentElementDiv->getElementsByTagName('div')->item(0);
                if ($imageContainer) {
                    $style = $imageContainer->getAttribute('style');
                    if (preg_match('/background-image:\s*url\([\'"](.+?)[\'"]\)/', $style, $matches)) {
                        $element['content'] = $matches[1];
                    }
                }
                break;

            case 'image_text':
                $imageTextContainer = $contentElementDiv->getElementsByTagName('div')->item(0);
                if ($imageTextContainer) {
                    $imageContainer = $imageTextContainer->getElementsByTagName('div')->item(0);
                    $textContainer = $imageTextContainer->getElementsByTagName('div')->item(1);
                    
                    if ($imageContainer) {
                        $style = $imageContainer->getAttribute('style');
                        if (preg_match('/background-image:\s*url\([\'"](.+?)[\'"]\)/', $style, $matches)) {
                            $element['content'] = $matches[1];
                        }
                    }
                    
                    if ($textContainer) {
                        $paragraph = $textContainer->getElementsByTagName('p')->item(0);
                        if ($paragraph) {
                            $element['text'] = $paragraph->textContent;
                        } else {
                            $element['text'] = $textContainer->textContent;
                        }
                    }
                }
                break;

            case 'image_list':
                $imageListContainer = $contentElementDiv->getElementsByTagName('div')->item(0);
                if ($imageListContainer) {
                    $imageContainer = $imageListContainer->getElementsByTagName('div')->item(0);
                    $listContainer = $imageListContainer->getElementsByTagName('ul')->item(0);
                    
                    if ($imageContainer) {
                        $style = $imageContainer->getAttribute('style');
                        if (preg_match('/background-image:\s*url\([\'"](.+?)[\'"]\)/', $style, $matches)) {
                            $element['content'] = $matches[1];
                        }
                    }
                    
                    if ($listContainer) {
                        $items = [];
                        foreach ($listContainer->getElementsByTagName('li') as $li) {
                            $items[] = $li->textContent;
                        }
                        $element['text'] = implode("\n", $items);
                    }
                }
                break;

            case 'quote':
                $blockquote = $contentElementDiv->getElementsByTagName('blockquote')->item(0);
                if ($blockquote) {
                    $element['content'] = $blockquote->textContent;
                    $cite = $blockquote->getElementsByTagName('cite')->item(0);
                    if ($cite) {
                        $element['title'] = trim(str_replace('—', '', $cite->textContent));
                    }
                }
                break;

            case 'list':
                $ul = $contentElementDiv->getElementsByTagName('ul')->item(0);
                if ($ul) {
                    $items = [];
                    foreach ($ul->getElementsByTagName('li') as $li) {
                        $items[] = $li->textContent;
                    }
                    $element['content'] = implode("\n", $items);
                }
                break;

            case 'text':
                $element['content'] = $contentElementDiv->textContent;
                break;
        }

        return $element;
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
                $content = trim(substr($line, 11));
                // Премахваме екранираните двоеточия и нови редове
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

        // Валидация на данните
        if (empty($title)) {
            error_log("Error: Empty presentation title");
            return null;
        }

        if (empty($slides)) {
            error_log("Error: No slides found");
            return null;
        }

        foreach ($slides as $slide) {
            if (empty($slide['title'])) {
                error_log("Error: Empty slide title");
                return null;
            }
            if (empty($slide['elements'])) {
                error_log("Error: Empty slide elements");
                return null;
            }
            foreach ($slide['elements'] as $element) {
                if (empty($element['type'])) {
                    error_log("Error: Empty element type");
                    return null;
                }
                if (empty($element['content'])) {
                    error_log("Error: Empty element content");
                    return null;
                }
            }
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