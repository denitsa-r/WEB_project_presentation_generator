<?php

class SlideElement {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getElementsBySlideId($slideId) {
        $stmt = $this->db->query('SELECT * FROM slide_elements WHERE slide_id = :slide_id ORDER BY element_order ASC', ['slide_id' => $slideId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addElement($data) {
        $stmt = $this->db->query(
            'INSERT INTO slide_elements (slide_id, type, content, title, text, style, element_order) 
             VALUES (:slide_id, :type, :content, :title, :text, :style, :element_order)',
            [
                'slide_id' => $data['slide_id'],
                'type' => $data['type'],
                'content' => $data['content'],
                'title' => $data['title'] ?? '',
                'text' => $data['text'] ?? '',
                'style' => $data['style'] ?? '{}',
                'element_order' => $data['element_order']
            ]
        );
        
        if($stmt) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function updateElement($data) {
        return $this->db->query(
            'UPDATE slide_elements SET type = :type, content = :content, element_order = :element_order WHERE id = :id',
            [
                'id' => $data['id'],
                'type' => $data['type'],
                'content' => $data['content'],
                'element_order' => $data['element_order']
            ]
        );
    }

    public function deleteElement($id) {
        return $this->db->query('DELETE FROM slide_elements WHERE id = :id', ['id' => $id]);
    }

    public function getElementById($id) {
        $stmt = $this->db->query('SELECT * FROM slide_elements WHERE id = :id', ['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePositions($slideId, $positions) {
        foreach($positions as $position => $elementId) {
            $this->db->query(
                'UPDATE slide_elements SET element_order = :element_order WHERE id = :id AND slide_id = :slide_id',
                [
                    'element_order' => $position,
                    'id' => $elementId,
                    'slide_id' => $slideId
                ]
            );
        }
        return true;
    }

    public function getMaxPosition($slideId) {
        $stmt = $this->db->query('SELECT MAX(element_order) as max_position FROM slide_elements WHERE slide_id = :slide_id', ['slide_id' => $slideId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['max_position'] ?? 0;
    }

    public function deleteElementsBySlideId($slideId) {
        return $this->db->query('DELETE FROM slide_elements WHERE slide_id = :slide_id', ['slide_id' => $slideId]);
    }

    public function copyElements($fromSlideId, $toSlideId) {
        $elements = $this->getElementsBySlideId($fromSlideId);
        
        foreach($elements as $element) {
            $this->db->query('INSERT INTO slide_elements (slide_id, type, content, element_order) VALUES (:slide_id, :type, :content, :element_order)');
            
            $this->db->bind(':slide_id', $toSlideId);
            $this->db->bind(':type', $element['type']);
            $this->db->bind(':content', $element['content']);
            $this->db->bind(':element_order', $element['element_order']);
            
            $this->db->execute();
        }
        
        return true;
    }
} 