<?php
/**
 * Authentication Handler
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->startSession();
    }
    
    private function startSession() {
        if (!isset($_SESSION) && session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }
    
    public function login($email, $password) {
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email AND role = 'admin'",
            ['email' => $email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Update last login
            $this->db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')],
                'id = :id',
                ['id' => $user['id']]
            );
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUserEmail() {
        return $_SESSION['user_email'] ?? null;
    }
    
    public function createUser($email, $password, $role = 'admin') {
        try {
            // Check if user already exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM users WHERE email = :email",
                ['email' => $email]
            );
            
            if ($existing) {
                throw new Exception("User with email {$email} already exists");
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            if ($hashedPassword === false) {
                throw new Exception("Failed to hash password");
            }
            
            $userId = $this->db->insert('users', [
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $userId;
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            throw $e;
        }
    }
}

