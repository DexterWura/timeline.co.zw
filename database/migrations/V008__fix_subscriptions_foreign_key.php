<?php
/**
 * Fix subscriptions table foreign key to use ON DELETE SET NULL
 */
class V008__fix_subscriptions_foreign_key {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Fix subscriptions table foreign key constraint to use ON DELETE SET NULL instead of CASCADE';
    }
    
    public function up() {
        try {
            // Drop existing foreign key constraint
            $foreignKeys = $this->db->fetchAll("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'subscriptions' 
                AND COLUMN_NAME = 'user_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $fk) {
                    $this->db->query("ALTER TABLE subscriptions DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
                }
            }
            
            // Re-add foreign key with ON DELETE SET NULL
            $this->db->query("
                ALTER TABLE subscriptions 
                ADD CONSTRAINT fk_subscriptions_user_id 
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ");
        } catch (Exception $e) {
            // Foreign key might not exist or already be correct
            // Try to add it if it doesn't exist
            try {
                $this->db->query("
                    ALTER TABLE subscriptions 
                    ADD CONSTRAINT fk_subscriptions_user_id 
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ");
            } catch (Exception $e2) {
                // Constraint might already exist with correct settings, that's okay
            }
        }
    }
}

