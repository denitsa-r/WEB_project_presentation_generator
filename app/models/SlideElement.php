<?php

class SlideElement {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Вземане на всички елементи за даден слайд
    public function getElementsBySlideId($slideId) {
        $this->db->query('SELECT * FROM slide_elements WHERE slide_id = :slide_id ORDER BY position ASC');
        $this->db->bind(':slide_id', $slideId);
        return $this->db->resultSet();
    }

    // Добавяне на нов елемент
    public function addElement($data) {
        $this->db->query('INSERT INTO slide_elements (slide_id, type, content, position) VALUES (:slide_id, :type, :content, :position)');
        
        $this->db->bind(':slide_id', $data['slide_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':position', $data['position']);

        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Редактиране на елемент
    public function updateElement($data) {
        $this->db->query('UPDATE slide_elements SET type = :type, content = :content, position = :position WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':position', $data['position']);

        return $this->db->execute();
    }

    // Изтриване на елемент
    public function deleteElement($id) {
        $this->db->query('DELETE FROM slide_elements WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Вземане на елемент по ID
    public function getElementById($id) {
        $this->db->query('SELECT * FROM slide_elements WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Обновяване на позициите на елементите
    public function updatePositions($slideId, $positions) {
        $this->db->query('UPDATE slide_elements SET position = :position WHERE id = :id AND slide_id = :slide_id');
        
        foreach($positions as $position => $elementId) {
            $this->db->bind(':position', $position);
            $this->db->bind(':id', $elementId);
            $this->db->bind(':slide_id', $slideId);
            $this->db->execute();
        }
        
        return true;
    }

    // Вземане на максималната позиция за даден слайд
    public function getMaxPosition($slideId) {
        $this->db->query('SELECT MAX(position) as max_position FROM slide_elements WHERE slide_id = :slide_id');
        $this->db->bind(':slide_id', $slideId);
        $result = $this->db->single();
        return $result->max_position ?? 0;
    }

    // Изтриване на всички елементи за даден слайд
    public function deleteElementsBySlideId($slideId) {
        $this->db->query('DELETE FROM slide_elements WHERE slide_id = :slide_id');
        $this->db->bind(':slide_id', $slideId);
        return $this->db->execute();
    }

    // Копиране на елементи от един слайд в друг
    public function copyElements($fromSlideId, $toSlideId) {
        $elements = $this->getElementsBySlideId($fromSlideId);
        
        foreach($elements as $element) {
            $this->db->query('INSERT INTO slide_elements (slide_id, type, content, position) VALUES (:slide_id, :type, :content, :position)');
            
            $this->db->bind(':slide_id', $toSlideId);
            $this->db->bind(':type', $element->type);
            $this->db->bind(':content', $element->content);
            $this->db->bind(':position', $element->position);
            
            $this->db->execute();
        }
        
        return true;
    }
} 