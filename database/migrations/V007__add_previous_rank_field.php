<?php
/**
 * Add previous_rank field to music_charts and videos tables
 */
class V007__add_previous_rank_field {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add previous_rank field to music_charts and videos tables for tracking rank changes';
    }
    
    public function up() {
        // Add previous_rank to music_charts
        try {
            $columns = $this->db->fetchAll("SHOW COLUMNS FROM music_charts LIKE 'previous_rank'");
            if (empty($columns)) {
                $this->db->query("ALTER TABLE music_charts ADD COLUMN previous_rank INT NULL AFTER rank");
                $this->db->query("CREATE INDEX idx_previous_rank ON music_charts(previous_rank)");
            }
        } catch (Exception $e) {
            // Column might already exist or table doesn't exist yet
        }
        
        // Add previous_rank to videos
        try {
            $columns = $this->db->fetchAll("SHOW COLUMNS FROM videos LIKE 'previous_rank'");
            if (empty($columns)) {
                $this->db->query("ALTER TABLE videos ADD COLUMN previous_rank INT NULL AFTER rank");
                $this->db->query("CREATE INDEX idx_previous_rank ON videos(previous_rank)");
            }
        } catch (Exception $e) {
            // Column might already exist or table doesn't exist yet
        }
    }
}

