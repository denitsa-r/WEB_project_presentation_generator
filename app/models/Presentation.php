<?php

class Presentation extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByWorkspaceId($workspaceId)
    {
        $sql = "SELECT * FROM presentations WHERE workspace_id = :workspace_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['workspace_id' => $workspaceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($workspaceId, $title, $language = 'bg', $theme = 'light')
    {
        try {
            $sql = "INSERT INTO presentations (workspace_id, title, language, theme) 
                    VALUES (:workspace_id, :title, :language, :theme)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'workspace_id' => $workspaceId,
                'title' => $title,
                'language' => $language,
                'theme' => $theme
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM presentations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $language, $theme)
    {
        try {
            $sql = "UPDATE presentations SET title = :title, language = :language, theme = :theme 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'title' => $title,
                'language' => $language,
                'theme' => $theme
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error updating presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            // Първо изтриваме всички слайдове, свързани с презентацията
            $sql = "DELETE FROM slides WHERE presentation_id = :presentation_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['presentation_id' => $id]);
            
            // След това изтриваме самата презентация
            $sql = "DELETE FROM presentations WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting presentation: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function hasAccess($userId, $presentationId)
    {
        // Проверяваме дали потребителят е собственик на презентацията
        $stmt = $this->db->query(
            "SELECT 1 FROM presentations WHERE id = ? AND user_id = ?",
            [$presentationId, $userId]
        );
        if ($stmt->fetch()) {
            return true;
        }

        // Проверяваме дали потребителят има достъп чрез работното пространство
        $stmt = $this->db->query(
            "SELECT 1 FROM presentations p
            JOIN workspaces w ON p.workspace_id = w.id
            JOIN user_workspaces uw ON w.id = uw.workspace_id
            WHERE p.id = ? AND uw.user_id = ?",
            [$presentationId, $userId]
        );
        return $stmt->fetch() !== false;
    }

    public function generateExportHtml($presentationId) {
        // Вземаме данните за презентацията
        $presentation = $this->getPresentationById($presentationId);
        if (!$presentation) {
            return false;
        }

        // Вземаме всички слайдове
        $slides = $this->getSlidesByPresentationId($presentationId);
        
        // Генерираме HTML
        $html = '<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($presentation->title) . '</title>
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
            flex: 0 0 40%;
            height: 200px;
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
            flex: 0 0 40%;
            height: 200px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content-element.type-image_list ul {
            flex: 1;
            margin: 0;
            padding-left: 20px;
        }
        .slide-content.full {
            width: 100%;
        }
        .slide-content.two-rows {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .slide-content.grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="presentation-view" data-theme="' . htmlspecialchars($presentation->theme) . '">';

        if (empty($slides)) {
            $html .= '<div class="empty-state">
                <i class="fas fa-file-alt fa-3x"></i>
                <p>Няма добавени слайдове.</p>
            </div>';
        } else {
            foreach ($slides as $slide) {
                $html .= '<div class="slide">
                    <h2 class="slide-title">' . htmlspecialchars($slide->title) . '</h2>
                    <div class="slide-content ' . htmlspecialchars($slide->layout ?? 'full') . '">';

                // Вземаме елементите на слайда
                $slideElements = $this->slideElementModel->getElementsBySlideId($slide->id);
                
                if (!empty($slideElements)) {
                    foreach ($slideElements as $element) {
                        $content = json_decode($element->content, true);
                        
                        $html .= '<div class="element-container">';
                        if (!empty($content['title'])) {
                            $html .= '<h3 class="element-title">' . htmlspecialchars($content['title']) . '</h3>';
                        }

                        switch ($element->type) {
                            case 'image':
                                $html .= '<div class="content-element image">
                                    <div class="image-container" style="background-image: url(\'' . htmlspecialchars($content['url']) . '\');"></div>
                                </div>';
                                break;
                            
                            case 'image_text':
                                $html .= '<div class="content-element type-image_text">
                                    <div class="image-text-container">
                                        <div class="image-container" style="background-image: url(\'' . htmlspecialchars($content['url']) . '\');"></div>
                                        <div class="text"><p>' . nl2br(htmlspecialchars($content['text'])) . '</p></div>
                                    </div>
                                </div>';
                                break;
                            
                            case 'image_list':
                                $html .= '<div class="content-element type-image_list">
                                    <div class="image-list-container">
                                        <div class="image-container" style="background-image: url(\'' . htmlspecialchars($content['url']) . '\');"></div>
                                        <ul>';
                                foreach (explode("\n", $content['text']) as $item) {
                                    if (trim($item) !== '') {
                                        $html .= '<li>' . htmlspecialchars($item) . '</li>';
                                    }
                                }
                                $html .= '</ul>
                                    </div>
                                </div>';
                                break;
                            
                            case 'quote':
                                $html .= '<div class="content-element type-quote">
                                    <blockquote>' . nl2br(htmlspecialchars($content['text']));
                                if (!empty($content['author'])) {
                                    $html .= '<cite>— ' . htmlspecialchars($content['author']) . '</cite>';
                                }
                                $html .= '</blockquote>
                                </div>';
                                break;
                            
                            default:
                                $html .= '<div class="content-element ' . $element->type . '">' . 
                                    nl2br(htmlspecialchars($content['text'])) . 
                                '</div>';
                        }
                        
                        $html .= '</div>';
                    }
                } else {
                    $html .= '<div class="empty-content">Няма добавено съдържание</div>';
                }

                $html .= '</div></div>';
            }
        }

        $html .= '</div></body></html>';
        
        return $html;
    }
} 