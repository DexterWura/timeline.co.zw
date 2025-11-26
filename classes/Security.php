<?php
/**
 * Security Handler - CSRF, Input Validation, Rate Limiting
 */
class Security {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public function requireCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCSRFToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }
    
    public function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return $this->sanitizeInput($item, $type);
            }, $input);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'html':
                return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public function rateLimit($key, $maxAttempts = 5, $timeWindow = 300) {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $now = time();
        $attempts = $_SESSION['rate_limit'][$key] ?? ['count' => 0, 'reset' => $now + $timeWindow];
        
        if ($now > $attempts['reset']) {
            $attempts = ['count' => 0, 'reset' => $now + $timeWindow];
        }
        
        if ($attempts['count'] >= $maxAttempts) {
            return false;
        }
        
        $attempts['count']++;
        $_SESSION['rate_limit'][$key] = $attempts;
        
        return true;
    }
    
    public function logSecurityEvent($event, $details = []) {
        $logFile = BASE_PATH . '/logs/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
    }
    
    public function preventXSS($data) {
        if (is_array($data)) {
            return array_map([$this, 'preventXSS'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    public function secureHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

