<?php
/**
 * Add awards and richest people tables
 */
class V004__add_awards_and_richest {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add awards and richest_people tables for awards and billionaire tracking';
    }
    
    public function up() {
        // Awards table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS awards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                award_name VARCHAR(255) NOT NULL,
                category VARCHAR(255),
                year INT NOT NULL,
                winner VARCHAR(255),
                nominees TEXT,
                description TEXT,
                source VARCHAR(255),
                source_url TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_award (award_name),
                INDEX idx_year (year),
                INDEX idx_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Richest people table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS richest_people (
                id INT AUTO_INCREMENT PRIMARY KEY,
                rank INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                net_worth BIGINT DEFAULT 0,
                source VARCHAR(255),
                country_code VARCHAR(2) DEFAULT 'US',
                chart_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_rank (rank),
                INDEX idx_country (country_code),
                INDEX idx_date (chart_date),
                INDEX idx_country_date (country_code, chart_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Hall of Fame table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS hall_of_fame (
                id INT AUTO_INCREMENT PRIMARY KEY,
                artist_name VARCHAR(255) NOT NULL,
                category VARCHAR(100),
                year_inducted INT,
                description TEXT,
                achievements TEXT,
                image_url TEXT,
                country_code VARCHAR(2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_artist (artist_name),
                INDEX idx_category (category),
                INDEX idx_year (year_inducted)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

