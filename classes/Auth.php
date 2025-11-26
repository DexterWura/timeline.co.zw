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
    
    public function login($email, $password, $requireAdmin = false) {
        $sql = "SELECT * FROM users WHERE email = :email";
        if ($requireAdmin) {
            $sql .= " AND role = 'admin'";
        }
        $sql .= " AND is_active = 1";
        
        $user = $this->db->fetchOne($sql, ['email' => $email]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'] ?? $user['email'];
            $_SESSION['username'] = $user['username'] ?? null;
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
    
    public function hasRole($role) {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    public function hasAnyRole($roles) {
        if (!$this->isLoggedIn() || !isset($_SESSION['user_role'])) {
            return false;
        }
        return in_array($_SESSION['user_role'], (array)$roles);
    }
    
    public function canWrite() {
        return $this->hasAnyRole(['admin', 'editor', 'writer']);
    }
    
    public function canEdit() {
        return $this->hasAnyRole(['admin', 'editor']);
    }
    
    public function canModerate() {
        return $this->hasAnyRole(['admin', 'moderator', 'editor']);
    }
    
    public function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    public function getUserName() {
        return $_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'User';
    }
    
    public function register($email, $password, $name = null, $username = null) {
        try {
            // Check if user already exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM users WHERE email = :email OR username = :username",
                ['email' => $email, 'username' => $username]
            );
            
            if ($existing) {
                throw new Exception("User with this email or username already exists");
            }
            
            // Generate username from email if not provided
            if (empty($username)) {
                $username = strtolower(explode('@', $email)[0]);
                // Ensure uniqueness
                $counter = 1;
                $originalUsername = $username;
                while ($this->db->fetchOne("SELECT id FROM users WHERE username = :username", ['username' => $username])) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            if ($hashedPassword === false) {
                throw new Exception("Failed to hash password");
            }
            
            // Generate email verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            $userId = $this->db->insert('users', [
                'email' => $email,
                'password' => $hashedPassword,
                'name' => $name ?? explode('@', $email)[0],
                'username' => $username,
                'role' => 'user',
                'email_verification_token' => $verificationToken,
                'is_active' => 1,
                'email_verified' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'id' => $userId,
                'email' => $email,
                'username' => $username,
                'verification_token' => $verificationToken
            ];
        } catch (Exception $e) {
            error_log("Error registering user: " . $e->getMessage());
            throw $e;
        }
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
    
    public function createUser($email, $password, $role = 'admin', $name = null, $username = null) {
        try {
            // Check if user already exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM users WHERE email = :email OR username = :username",
                ['email' => $email, 'username' => $username ?? '']
            );
            
            if ($existing) {
                throw new Exception("User with email {$email} or username already exists");
            }
            
            // Generate username if not provided
            if (empty($username)) {
                $username = strtolower(explode('@', $email)[0]);
                $counter = 1;
                $originalUsername = $username;
                while ($this->db->fetchOne("SELECT id FROM users WHERE username = :username", ['username' => $username])) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            if ($hashedPassword === false) {
                throw new Exception("Failed to hash password");
            }
            
            $userId = $this->db->insert('users', [
                'email' => $email,
                'password' => $hashedPassword,
                'name' => $name ?? explode('@', $email)[0],
                'username' => $username,
                'role' => $role,
                'is_active' => 1,
                'email_verified' => 1, // Admin-created users are pre-verified
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $userId;
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            throw $e;
        }
    }
}

