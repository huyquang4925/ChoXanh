<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * News Model
 */
class NewsModel extends Model {
    protected $table = 'news';
    
    /**
     * Get all news with author
     */
    public function getAllNews() {
        $sql = "SELECT n.*, u.username AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.created_at DESC";
        $result = $this->conn->query($sql);
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    /**
     * Get news by ID
     */
    public function getNews($id) {
        $id = intval($id);
        $sql = "SELECT n.*, u.username AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.id = {$id}";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Add news
     */
    public function addNews($title, $content, $image, $authorId) {
        return $this->insert([
            'title' => $title,
            'content' => $content,
            'image' => $image,
            'author_id' => $authorId
        ]);
    }
    
    /**
     * Update news
     */
    public function updateNews($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Delete news
     */
    public function deleteNews($id) {
        return $this->delete($id);
    }
}
?>
