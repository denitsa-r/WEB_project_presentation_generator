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

        if ($presentation->user_id != $_SESSION['user_id']) {
            redirect('presentations');
        }

        $html = $this->presentationModel->generateExportHtml($id);
        
        if (!$html) {
            redirect('presentations');
        }

        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $presentation->title . '.html"');
        echo $html;
        exit;
    }
} 