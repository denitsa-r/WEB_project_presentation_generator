<?php

class Presentations {

    public function export($id) {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $presentation = $this->presentationModel->getPresentationById($id);
        
        if (!$presentation) {
            redirect('presentations');
        }

        // Проверяваме дали потребителят е собственик на презентацията
        if ($presentation->user_id != $_SESSION['user_id']) {
            redirect('presentations');
        }

        // Генерираме HTML
        $html = $this->presentationModel->generateExportHtml($id);
        
        if (!$html) {
            redirect('presentations');
        }

        // Изпращаме файла за изтегляне
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $presentation->title . '.html"');
        echo $html;
        exit;
    }
} 