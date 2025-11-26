<?php
/**
 * Add user roles, subscriptions, and user profile fields
 */
class V005__add_user_roles_and_subscriptions {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add user roles (writer, editor, moderator), subscriptions table, and user profile fields';
    }
    
    public function up() {
        // Update users table to support more roles and add profile fields
        // Check and modify role enum
        $this->db->query("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('admin', 'editor', 'writer', 'moderator', 'user') DEFAULT 'user'
        ");
        
        // Add columns if they don't exist (check first)
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'name'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN name VARCHAR(255) AFTER email");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'username'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN username VARCHAR(100) UNIQUE AFTER name");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'avatar_url'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN avatar_url TEXT AFTER username");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'bio'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN bio TEXT AFTER avatar_url");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'is_active'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT 1 AFTER bio");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'email_verified'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT 0 AFTER is_active");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'email_verification_token'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) AFTER email_verified");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'reset_token'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) AFTER email_verification_token");
        }
        
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM users LIKE 'reset_token_expires'");
        if (empty($columns)) {
            $this->db->query("ALTER TABLE users ADD COLUMN reset_token_expires TIMESTAMP NULL AFTER reset_token");
        }
        
        // Add indexes if they don't exist
        $indexes = $this->db->fetchAll("SHOW INDEXES FROM users WHERE Key_name = 'idx_username'");
        if (empty($indexes)) {
            $this->db->query("ALTER TABLE users ADD INDEX idx_username (username)");
        }
        
        $indexes = $this->db->fetchAll("SHOW INDEXES FROM users WHERE Key_name = 'idx_role'");
        if (empty($indexes)) {
            $this->db->query("ALTER TABLE users ADD INDEX idx_role (role)");
        }
        
        // Subscriptions table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                email VARCHAR(255) NOT NULL,
                subscription_type ENUM('newsletter', 'updates', 'premium') DEFAULT 'newsletter',
                status ENUM('active', 'inactive', 'unsubscribed') DEFAULT 'active',
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                unsubscribed_at TIMESTAMP NULL,
                unsubscribe_token VARCHAR(255) UNIQUE,
                source VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user (user_id),
                INDEX idx_email (email),
                INDEX idx_status (status),
                INDEX idx_token (unsubscribe_token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // User permissions table (for fine-grained permissions)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS user_permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                permission VARCHAR(100) NOT NULL,
                granted BOOLEAN DEFAULT 1,
                granted_by INT,
                granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
                UNIQUE KEY unique_user_permission (user_id, permission),
                INDEX idx_user (user_id),
                INDEX idx_permission (permission)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

