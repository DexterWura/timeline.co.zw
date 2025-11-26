<?php
/**
 * Blog Manager
 */
class Blog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($title, $content, $authorId, $status = 'draft', $metaDescription = '', $metaKeywords = '') {
        $slug = $this->generateSlug($title);
        $excerpt = $this->generateExcerpt($content);
        
        // Add SEO fields if they don't exist in table
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'author_id' => $authorId,
            'status' => $status,
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null
        ];
        
        // Add SEO fields if table has them
        try {
            $this->db->query("SELECT meta_description FROM blogs LIMIT 1");
            $data['meta_description'] = $metaDescription ?: $excerpt;
            $data['meta_keywords'] = $metaKeywords;
        } catch (Exception $e) {
            // Columns don't exist yet, will be added in migration
        }
        
        return $this->db->insert('blogs', $data);
    }
    
    public function update($id, $data) {
        if (isset($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }
        if (isset($data['content'])) {
            $data['excerpt'] = $this->generateExcerpt($data['content']);
        }
        
        return $this->db->update('blogs', $data, 'id = :id', ['id' => $id]);
    }
    
    public function get($id) {
        return $this->db->fetchOne("SELECT * FROM blogs WHERE id = :id", ['id' => $id]);
    }
    
    public function getBySlug($slug) {
        return $this->db->fetchOne("SELECT * FROM blogs WHERE slug = :slug AND status = 'published'", ['slug' => $slug]);
    }
    
    public function getAll($status = null) {
        $sql = "SELECT b.*, u.email as author_email FROM blogs b LEFT JOIN users u ON b.author_id = u.id";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE b.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->delete('blogs', 'id = :id', ['id' => $id]);
    }
    
    private function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $result = $this->db->fetchOne("SELECT id FROM blogs WHERE slug = :slug", ['slug' => $slug]);
        return $result !== false;
    }
    
    private function generateExcerpt($content, $length = 150) {
        $excerpt = strip_tags($content);
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length) . '...';
        }
        return $excerpt;
    }
}

