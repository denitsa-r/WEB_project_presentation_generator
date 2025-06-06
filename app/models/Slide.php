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
            "SELECT * FROM slides WHERE presentation_id = ? ORDER BY slide_order ASC",
            [$presentationId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO slides (presentation_id, title, layout, content) VALUES (:presentation_id, :title, :layout, :content)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'presentation_id' => $data['presentation_id'],
            'title' => $data['title'],
            'layout' => $data['layout'],
            'content' => $data['content']
        ]) ? $this->db->lastInsertId() : false;
    }

    public function getById($id)
    {
        $stmt = $this->db->query(
            "SELECT * FROM slides WHERE id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE slides SET title = :title, layout = :layout, content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'layout' => $data['layout'],
            'content' => $data['content']
        ]);
    }

    public function delete($id)
    {
        try {
            $this->db->query(
                "DELETE FROM slides WHERE id = ?",
                [$id]
            );
            return true;
        } catch (PDOException $e) {
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

    public function updateNavigation($id, $prevSlideId, $nextSlideId)
    {
        try {
            $this->db->query(
                "UPDATE slides SET prev_slide_id = ?, next_slide_id = ? WHERE id = ?",
                [$prevSlideId, $nextSlideId, $id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
} 