<?php
/**
 * Ensure subscriptions.user_id column is properly nullable
 */
class V009__ensure_subscriptions_user_id_nullable {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Ensure subscriptions.user_id column is properly set to allow NULL values';
    }
    
    public function up() {
        try {
            // Check current column definition
            $columns = $this->db->fetchAll("SHOW COLUMNS FROM subscriptions WHERE Field = 'user_id'");
            
            if (!empty($columns)) {
                $column = $columns[0];
                // If Null is 'NO', we need to modify it
                if ($column['Null'] === 'NO') {
                    $this->db->query("ALTER TABLE subscriptions MODIFY COLUMN user_id INT NULL");
                }
            } else {
                // Column doesn't exist, add it
                $this->db->query("ALTER TABLE subscriptions ADD COLUMN user_id INT NULL AFTER id");
            }
        } catch (Exception $e) {
            // Table might not exist yet, that's okay - it will be created by V005
        }
    }
}

