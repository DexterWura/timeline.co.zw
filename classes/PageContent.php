<?php
/**
 * Page Content Manager
 */
class PageContent {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getPage($pageKey) {
        return $this->db->fetchOne(
            "SELECT * FROM page_content WHERE page_key = :key AND is_active = 1",
            ['key' => $pageKey]
        );
    }
    
    public function getAllPages() {
        return $this->db->fetchAll(
            "SELECT * FROM page_content ORDER BY page_key ASC"
        );
    }
    
    public function createOrUpdate($pageKey, $pageTitle, $content, $metaDescription = null, $metaKeywords = null, $userId = null) {
        $existing = $this->db->fetchOne(
            "SELECT id FROM page_content WHERE page_key = :key",
            ['key' => $pageKey]
        );
        
        if ($existing) {
            return $this->db->update('page_content', [
                'page_title' => $pageTitle,
                'content' => $content,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
                'updated_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'page_key = :key', ['key' => $pageKey]);
        } else {
            return $this->db->insert('page_content', [
                'page_key' => $pageKey,
                'page_title' => $pageTitle,
                'content' => $content,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
                'created_by' => $userId,
                'updated_by' => $userId,
                'is_active' => 1
            ]);
        }
    }
    
    public function toggleActive($pageKey, $isActive) {
        return $this->db->update('page_content', [
            'is_active' => $isActive ? 1 : 0
        ], 'page_key = :key', ['key' => $pageKey]);
    }
    
    public function delete($pageKey) {
        return $this->db->delete('page_content', 'page_key = :key', ['key' => $pageKey]);
    }
}

