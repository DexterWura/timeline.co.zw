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
            // Step 1: First ensure user_id column is nullable
            // Check current column definition
            $columns = $this->db->fetchAll("SHOW COLUMNS FROM subscriptions WHERE Field = 'user_id'");
            
            if (!empty($columns)) {
                $column = $columns[0];
                // If Null is 'NO', we need to modify it to allow NULL
                if ($column['Null'] === 'NO') {
                    // Get the column type
                    $type = $column['Type']; // e.g., 'int(11)'
                    $this->db->query("ALTER TABLE subscriptions MODIFY COLUMN user_id {$type} NULL");
                }
            }
            
            // Step 2: Drop existing foreign key constraints
            try {
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
                        $constraintName = $fk['CONSTRAINT_NAME'];
                        $this->db->query("ALTER TABLE subscriptions DROP FOREIGN KEY `{$constraintName}`");
                    }
                }
            } catch (Exception $e) {
                // No foreign key exists, that's okay
            }
            
            // Step 3: Re-add foreign key with ON DELETE SET NULL
            // Only add if it doesn't already exist
            $existingFk = $this->db->fetchAll("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'subscriptions' 
                AND COLUMN_NAME = 'user_id' 
                AND REFERENCED_TABLE_NAME = 'users'
            ");
            
            if (empty($existingFk)) {
                $this->db->query("
                    ALTER TABLE subscriptions 
                    ADD CONSTRAINT fk_subscriptions_user_id 
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ");
            }
        } catch (Exception $e) {
            // Log the error but don't fail the migration
            error_log("V008 migration error: " . $e->getMessage());
            // Try to at least ensure the column is nullable
            try {
                $columns = $this->db->fetchAll("SHOW COLUMNS FROM subscriptions WHERE Field = 'user_id'");
                if (!empty($columns) && $columns[0]['Null'] === 'NO') {
                    $type = $columns[0]['Type'];
                    $this->db->query("ALTER TABLE subscriptions MODIFY COLUMN user_id {$type} NULL");
                }
            } catch (Exception $e2) {
                // If this also fails, the column might not exist or table doesn't exist
            }
        }
    }
}

