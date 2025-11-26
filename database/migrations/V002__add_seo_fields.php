<?php
/**
 * Add SEO fields to blogs and news_articles
 */
class V002__add_seo_fields {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add SEO fields (meta_description, meta_keywords) to blogs and news_articles tables';
    }
    
    public function up() {
        // Add SEO fields to blogs table
        try {
            $this->db->query("ALTER TABLE blogs ADD COLUMN meta_description TEXT AFTER excerpt");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        try {
            $this->db->query("ALTER TABLE blogs ADD COLUMN meta_keywords VARCHAR(255) AFTER meta_description");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        // Add SEO fields to news_articles table
        try {
            $this->db->query("ALTER TABLE news_articles ADD COLUMN meta_description TEXT AFTER excerpt");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        try {
            $this->db->query("ALTER TABLE news_articles ADD COLUMN meta_keywords VARCHAR(255) AFTER meta_description");
        } catch (Exception $e) {
            // Column might already exist
        }
    }
}

