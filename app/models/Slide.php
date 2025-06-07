<?php

class Slide extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByPresentationId($presentationId)
    {
        error_log("Getting slides for presentation ID: " . $presentationId);
        
        $sql = "SELECT s.*, se.id as element_id, se.type as element_type, 
                se.title as element_title, se.content as element_content, 
                se.text as element_text, se.style as element_style, 
                se.element_order
                FROM slides s 
                LEFT JOIN slide_elements se ON s.id = se.slide_id 
                WHERE s.presentation_id = :presentation_id 
                ORDER BY s.slide_order, se.element_order";
                
        error_log("SQL Query: " . $sql);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['presentation_id' => $presentationId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Raw slides data: " . print_r($rows, true));
        
        $slides = [];
        $currentSlide = null;
        
        foreach ($rows as $row) {
            if ($currentSlide === null || $currentSlide['id'] !== $row['id']) {
                if ($currentSlide !== null) {
                    $slides[] = $currentSlide;
                }
                
                $currentSlide = [
                    'id' => $row['id'],
                    'presentation_id' => $row['presentation_id'],
                    'title' => $row['title'],
                    'slide_order' => $row['slide_order'],
                    'layout' => $row['layout'],
                    'elements' => []
                ];
            }
            
            if (!empty($row['element_id'])) {
                $element = [
                    'id' => $row['element_id'],
                    'type' => $row['element_type'],
                    'title' => $row['element_title'],
                    'content' => $row['element_content'],
                    'text' => $row['element_text'],
                    'style' => json_decode($row['element_style'] ?? '{}', true),
                    'element_order' => $row['element_order']
                ];
                
                error_log("Created element: " . print_r($element, true));
                
                $currentSlide['elements'][] = $element;
            }
        }
        
        if ($currentSlide !== null) {
            $slides[] = $currentSlide;
        }
        
        error_log("Final slides data: " . print_r($slides, true));
        
        return $slides;
    }

    public function create($data)
    {
        try {
            error_log("Attempting to create slide with data: " . print_r($data, true));
            error_log("Database connection details: " . DB_HOST . ", " . DB_NAME . ", " . DB_USER);
            
            $this->db->beginTransaction();
            
            // Създаване на слайда
            $sql = "INSERT INTO slides (presentation_id, title, slide_order, layout) 
                    VALUES (:presentation_id, :title, :slide_order, :layout)";
            error_log("SQL for slide creation: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            
            $slideOrder = $data['slide_order'] ?? $this->getNextOrder($data['presentation_id']);
            error_log("Using slide order: " . $slideOrder);
            
            $params = [
                'presentation_id' => $data['presentation_id'],
                'title' => $data['title'],
                'slide_order' => $slideOrder,
                'layout' => $data['layout']
            ];
            error_log("Parameters for slide creation: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            error_log("Slide creation result: " . ($result ? "success" : "failed"));
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to create slide: " . implode(", ", $stmt->errorInfo()));
            }
            
            $slideId = $this->db->lastInsertId();
            error_log("Created slide with ID: " . $slideId);
            
            // Създаване на елементите
            if (!empty($data['elements'])) {
                $sql = "INSERT INTO slide_elements (slide_id, element_order, type, title, content, text, style) 
                        VALUES (:slide_id, :element_order, :type, :title, :content, :text, :style)";
                error_log("SQL for element creation: " . $sql);
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['elements'] as $order => $element) {
                    error_log("Creating element: " . print_r($element, true));
                    $params = [
                        'slide_id' => $slideId,
                        'element_order' => $order,
                        'type' => $element['type'],
                        'title' => $element['title'] ?? null,
                        'content' => $element['content'] ?? null,
                        'text' => $element['text'] ?? null,
                        'style' => json_encode($element['style'] ?? null)
                    ];
                    error_log("Parameters for element creation: " . print_r($params, true));
                    
                    $result = $stmt->execute($params);
                    error_log("Element creation result: " . ($result ? "success" : "failed"));
                    
                    if (!$result) {
                        error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                        throw new Exception("Failed to create element: " . implode(", ", $stmt->errorInfo()));
                    }
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
        $sql = "SELECT s.*, 
                GROUP_CONCAT(se.id ORDER BY se.element_order) as element_ids,
                GROUP_CONCAT(se.type ORDER BY se.element_order) as element_types,
                GROUP_CONCAT(se.title ORDER BY se.element_order) as element_titles,
                GROUP_CONCAT(se.content ORDER BY se.element_order) as element_contents,
                GROUP_CONCAT(se.text ORDER BY se.element_order) as element_texts,
                GROUP_CONCAT(se.style ORDER BY se.element_order) as element_styles
         FROM slides s
         LEFT JOIN slide_elements se ON s.id = se.slide_id
         WHERE s.id = :id
         GROUP BY s.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $slide = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($slide) {
            $slide['elements'] = [];
            if (!empty($slide['element_ids'])) {
                $ids = explode(',', $slide['element_ids'] ?? '');
                $types = explode(',', $slide['element_types'] ?? '');
                $titles = explode(',', $slide['element_titles'] ?? '');
                $contents = explode(',', $slide['element_contents'] ?? '');
                $texts = explode(',', $slide['element_texts'] ?? '');
                $styles = explode(',', $slide['element_styles'] ?? '');
                
                for ($i = 0; $i < count($ids); $i++) {
                    $slide['elements'][] = [
                        'id' => $ids[$i] ?? null,
                        'type' => $types[$i] ?? null,
                        'title' => $titles[$i] ?? null,
                        'content' => $contents[$i] ?? null,
                        'text' => $texts[$i] ?? null,
                        'style' => json_decode($styles[$i] ?? '{}', true)
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
            error_log("Attempting to update slide with ID: " . $id);
            error_log("Update data: " . print_r($data, true));
            
            $this->db->beginTransaction();
            
            // Обновяване на слайда
            $sql = "UPDATE slides SET title = :title, layout = :layout WHERE id = :id";
            error_log("SQL for slide update: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'id' => $id,
                'title' => $data['title'],
                'layout' => $data['layout']
            ]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to update slide: " . implode(", ", $stmt->errorInfo()));
            }
            
            // Изтриване на старите елементи
            $sql = "DELETE FROM slide_elements WHERE slide_id = :slide_id";
            error_log("SQL for deleting old elements: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['slide_id' => $id]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to delete old elements: " . implode(", ", $stmt->errorInfo()));
            }
            
            // Създаване на новите елементи
            if (!empty($data['elements'])) {
                $sql = "INSERT INTO slide_elements (slide_id, element_order, type, title, content, text, style) 
                        VALUES (:slide_id, :element_order, :type, :title, :content, :text, :style)";
                error_log("SQL for element creation: " . $sql);
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['elements'] as $order => $element) {
                    error_log("Creating element: " . print_r($element, true));
                    $params = [
                        'slide_id' => $id,
                        'element_order' => $order,
                        'type' => $element['type'],
                        'title' => $element['title'] ?? null,
                        'content' => $element['content'] ?? null,
                        'text' => $element['text'] ?? null,
                        'style' => json_encode($element['style'] ?? null)
                    ];
                    error_log("Parameters for element creation: " . print_r($params, true));
                    
                    $result = $stmt->execute($params);
                    error_log("Element creation result: " . ($result ? "success" : "failed"));
                    
                    if (!$result) {
                        error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                        throw new Exception("Failed to create element: " . implode(", ", $stmt->errorInfo()));
                    }
                }
            }
            
            $this->db->commit();
            error_log("Successfully updated slide and its elements");
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error updating slide: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            error_log("Attempting to delete slide with ID: " . $id);
            
            $this->db->beginTransaction();
            
            // Изтриване на елементите
            $sql = "DELETE FROM slide_elements WHERE slide_id = :slide_id";
            error_log("SQL for deleting elements: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['slide_id' => $id]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to delete elements: " . implode(", ", $stmt->errorInfo()));
            }
            
            // Изтриване на слайда
            $sql = "DELETE FROM slides WHERE id = :id";
            error_log("SQL for deleting slide: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $id]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to delete slide: " . implode(", ", $stmt->errorInfo()));
            }
            
            $this->db->commit();
            error_log("Successfully deleted slide and its elements");
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error deleting slide: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateOrder($id, $newOrder)
    {
        try {
            error_log("Attempting to update slide order. ID: " . $id . ", New order: " . $newOrder);
            
            $sql = "UPDATE slides SET slide_order = :slide_order WHERE id = :id";
            error_log("SQL for updating slide order: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'id' => $id,
                'slide_order' => $newOrder
            ]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to update slide order: " . implode(", ", $stmt->errorInfo()));
            }
            
            error_log("Successfully updated slide order");
            return true;
            
        } catch (Exception $e) {
            error_log("Error updating slide order: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function getNextOrder($presentationId)
    {
        try {
            error_log("Getting next order for presentation ID: " . $presentationId);
            
            $sql = "SELECT MAX(slide_order) as max_order FROM slides WHERE presentation_id = :presentation_id";
            error_log("SQL for getting max order: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['presentation_id' => $presentationId]);
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to get max order: " . implode(", ", $stmt->errorInfo()));
            }
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextOrder = ($row['max_order'] ?? 0) + 1;
            
            error_log("Next order will be: " . $nextOrder);
            return $nextOrder;
            
        } catch (Exception $e) {
            error_log("Error getting next order: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
} 