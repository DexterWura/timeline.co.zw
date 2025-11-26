<?php
/**
 * Initial Database Schema
 */
class V001__initial_schema {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Initial database schema with users, settings, charts, and cache tables';
    }
    
    public function up() {
        // Users table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Settings table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(255) NOT NULL UNIQUE,
                setting_value TEXT,
                setting_type VARCHAR(50) DEFAULT 'text',
                description TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Music charts table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS music_charts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                rank INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                artist VARCHAR(255) NOT NULL,
                genre VARCHAR(100),
                weeks_on_chart INT DEFAULT 0,
                peak_position INT,
                last_week_position INT,
                streams BIGINT DEFAULT 0,
                play_count BIGINT DEFAULT 0,
                artwork_url TEXT,
                is_new BOOLEAN DEFAULT 0,
                is_re_entry BOOLEAN DEFAULT 0,
                chart_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_rank (rank),
                INDEX idx_chart_date (chart_date),
                INDEX idx_artist (artist)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Videos table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS videos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                rank INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                artist VARCHAR(255) NOT NULL,
                category VARCHAR(100),
                views BIGINT DEFAULT 0,
                likes BIGINT DEFAULT 0,
                duration VARCHAR(20),
                upload_date VARCHAR(100),
                description TEXT,
                thumbnail_url TEXT,
                video_id VARCHAR(255),
                channel_id VARCHAR(255),
                chart_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_rank (rank),
                INDEX idx_chart_date (chart_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // API cache table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS api_cache (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cache_key VARCHAR(255) NOT NULL UNIQUE,
                cache_data LONGTEXT,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_key (cache_key),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Blogs table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS blogs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content LONGTEXT,
                excerpt TEXT,
                featured_image TEXT,
                author_id INT,
                status ENUM('draft', 'published') DEFAULT 'draft',
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_status (status),
                INDEX idx_author (author_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // News articles table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS news_articles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content LONGTEXT,
                excerpt TEXT,
                image_url TEXT,
                source VARCHAR(255),
                source_url TEXT,
                author_id INT,
                category VARCHAR(100),
                is_from_api BOOLEAN DEFAULT 0,
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_category (category),
                INDEX idx_published (published_at),
                INDEX idx_source (is_from_api)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Sitemap settings table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS sitemap_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                generation_frequency INT DEFAULT 1,
                last_generated TIMESTAMP NULL,
                auto_generate BOOLEAN DEFAULT 1,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Insert default sitemap settings
        try {
            $this->db->insert('sitemap_settings', [
                'generation_frequency' => 1,
                'auto_generate' => 1
            ]);
        } catch (Exception $e) {
            // Settings might already exist
        }
    }
}

