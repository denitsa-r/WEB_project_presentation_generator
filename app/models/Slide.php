<?php

class Slide extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByPresentationId($presentationId)
    {
        $stmt = $this->db->query(
            "SELECT s.*, 
                    GROUP_CONCAT(se.id ORDER BY se.element_order) as element_ids,
                    GROUP_CONCAT(se.type ORDER BY se.element_order) as element_types,
                    GROUP_CONCAT(se.title ORDER BY se.element_order) as element_titles,
                    GROUP_CONCAT(se.content ORDER BY se.element_order) as element_contents,
                    GROUP_CONCAT(se.text ORDER BY se.element_order) as element_texts,
                    GROUP_CONCAT(se.style ORDER BY se.element_order) as element_styles
             FROM slides s
             LEFT JOIN slide_elements se ON s.id = se.slide_id
             WHERE s.presentation_id = ?
             GROUP BY s.id
             ORDER BY s.slide_order ASC",
            [$presentationId]
        );
        
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Преобразуване на конкатенираните стойности в масиви
        foreach ($slides as &$slide) {
            $slide['elements'] = [];
            if ($slide['element_ids']) {
                $ids = explode(',', $slide['element_ids']);
                $types = explode(',', $slide['element_types']);
                $titles = explode(',', $slide['element_titles']);
                $contents = explode(',', $slide['element_contents']);
                $texts = explode(',', $slide['element_texts']);
                $styles = explode(',', $slide['element_styles']);
                
                for ($i = 0; $i < count($ids); $i++) {
                    $slide['elements'][] = [
                        'id' => $ids[$i],
                        'type' => $types[$i],
                        'title' => $titles[$i],
                        'content' => $contents[$i],
                        'text' => $texts[$i],
                        'style' => json_decode($styles[$i], true)
                    ];
                }
            }
            
            // Премахване на излишните колони
            unset($slide['element_ids'], $slide['element_types'], $slide['element_titles'], 
                  $slide['element_contents'], $slide['element_texts'], $slide['element_styles']);
        }
        
        return $slides;
    }

    public function create($data)
    {
        try {
            error_log("Attempting to create slide with data: " . print_r($data, true));
            
            $this->db->beginTransaction();
            
            // Създаване на слайда
            $sql = "INSERT INTO slides (presentation_id, title, slide_order, layout) 
                    VALUES (:presentation_id, :title, :slide_order, :layout)";
            $stmt = $this->db->prepare($sql);
            
            $slideOrder = $data['slide_order'] ?? $this->getNextOrder($data['presentation_id']);
            error_log("Using slide order: " . $slideOrder);
            
            $stmt->execute([
                'presentation_id' => $data['presentation_id'],
                'title' => $data['title'],
                'slide_order' => $slideOrder,
                'layout' => $data['layout']
            ]);
            
            $slideId = $this->db->lastInsertId();
            error_log("Created slide with ID: " . $slideId);
            
            // Създаване на елементите
            if (!empty($data['elements'])) {
                $sql = "INSERT INTO slide_elements (slide_id, element_order, type, title, content, text, style) 
                        VALUES (:slide_id, :element_order, :type, :title, :content, :text, :style)";
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['elements'] as $order => $element) {
                    error_log("Creating element: " . print_r($element, true));
                    $stmt->execute([
                        'slide_id' => $slideId,
                        'element_order' => $order,
                        'type' => $element['type'],
                        'title' => $element['title'] ?? null,
                        'content' => $element['content'] ?? null,
                        'text' => $element['text'] ?? null,
                        'style' => json_encode($element['style'] ?? null)
                    ]);
                }
            }
            
            $this->db->commit();
            error_log("Successfully created slide and its elements");
            return $slideId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating slide: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getById($id)
    {
        $stmt = $this->db->query(
            "SELECT s.*, 
                    GROUP_CONCAT(se.id ORDER BY se.element_order) as element_ids,
                    GROUP_CONCAT(se.type ORDER BY se.element_order) as element_types,
                    GROUP_CONCAT(se.title ORDER BY se.element_order) as element_titles,
                    GROUP_CONCAT(se.content ORDER BY se.element_order) as element_contents,
                    GROUP_CONCAT(se.text ORDER BY se.element_order) as element_texts,
                    GROUP_CONCAT(se.style ORDER BY se.element_order) as element_styles
             FROM slides s
             LEFT JOIN slide_elements se ON s.id = se.slide_id
             WHERE s.id = ?
             GROUP BY s.id",
            [$id]
        );
        
        $slide = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($slide) {
            $slide['elements'] = [];
            if ($slide['element_ids']) {
                $ids = explode(',', $slide['element_ids']);
                $types = explode(',', $slide['element_types']);
                $titles = explode(',', $slide['element_titles']);
                $contents = explode(',', $slide['element_contents']);
                $texts = explode(',', $slide['element_texts']);
                $styles = explode(',', $slide['element_styles']);
                
                for ($i = 0; $i < count($ids); $i++) {
                    $slide['elements'][] = [
                        'id' => $ids[$i],
                        'type' => $types[$i],
                        'title' => $titles[$i],
                        'content' => $contents[$i],
                        'text' => $texts[$i],
                        'style' => json_decode($styles[$i], true)
                    ];
                }
            }
            
            unset($slide['element_ids'], $slide['element_types'], $slide['element_titles'], 
                  $slide['element_contents'], $slide['element_texts'], $slide['element_styles']);
        }
        
        return $slide;
    }

    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();
            
            // Обновяване на слайда
            $sql = "UPDATE slides SET title = :title, layout = :layout WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            $stmt->execute([
                'id' => $id,
                'title' => $data['title'],
                'layout' => $data['layout']
            ]);
            
            // Изтриване на старите елементи
            $this->db->query("DELETE FROM slide_elements WHERE slide_id = ?", [$id]);
            
            // Създаване на новите елементи
            if (!empty($data['elements'])) {
                $sql = "INSERT INTO slide_elements (slide_id, element_order, type, title, content, text, style) 
                        VALUES (:slide_id, :element_order, :type, :title, :content, :text, :style)";
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['elements'] as $order => $element) {
                    $stmt->execute([
                        'slide_id' => $id,
                        'element_order' => $order,
                        'type' => $element['type'],
                        'title' => $element['title'] ?? null,
                        'content' => $element['content'] ?? null,
                        'text' => $element['text'] ?? null,
                        'style' => json_encode($element['style'] ?? null)
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $this->db->beginTransaction();
            
            // Изтриване на елементите
            $this->db->query("DELETE FROM slide_elements WHERE slide_id = ?", [$id]);
            
            // Изтриване на слайда
            $this->db->query("DELETE FROM slides WHERE id = ?", [$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateOrder($id, $newOrder)
    {
        try {
            $this->db->query(
                "UPDATE slides SET slide_order = ? WHERE id = ?",
                [$newOrder, $id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function getNextOrder($presentationId)
    {
        $stmt = $this->db->query(
            "SELECT MAX(slide_order) as max_order FROM slides WHERE presentation_id = ?",
            [$presentationId]
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['max_order'] ?? 0) + 1;
    }
} 