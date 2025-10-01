<?php
// Secure API Configuration
// This file should be included in all API endpoints

// Security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS configuration - restrict to specific domains
$allowed_origins = [
    'https://timeline.co.zw',
    'https://www.timeline.co.zw',
    'http://localhost:3000', // For development
    'http://127.0.0.1:3000'  // For development
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header('Access-Control-Allow-Origin: https://timeline.co.zw');
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Rate limiting (simple implementation)
function checkRateLimit($identifier, $limit = 100, $window = 3600) {
    $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    $current_time = time();
    
    if (file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true);
        if ($current_time - $data['first_request'] < $window) {
            if ($data['count'] >= $limit) {
                return false;
            }
            $data['count']++;
        } else {
            $data = ['count' => 1, 'first_request' => $current_time];
        }
    } else {
        $data = ['count' => 1, 'first_request' => $current_time];
    }
    
    file_put_contents($cache_file, json_encode($data));
    return true;
}

// Input validation
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        if (!isset($data[$field]) && $rule['required']) {
            $errors[] = "Field '$field' is required";
            continue;
        }
        
        if (isset($data[$field])) {
            $value = $data[$field];
            
            // Sanitize input
            $value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            
            // Validate based on rules
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "Field '$field' must be a valid email";
                        }
                        break;
                    case 'country_code':
                        if (!preg_match('/^[A-Z]{2}$/', $value)) {
                            $errors[] = "Field '$field' must be a valid country code";
                        }
                        break;
                    case 'language_code':
                        if (!preg_match('/^[a-z]{2}$/', $value)) {
                            $errors[] = "Field '$field' must be a valid language code";
                        }
                        break;
                    case 'string':
                        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                            $errors[] = "Field '$field' exceeds maximum length";
                        }
                        break;
                }
            }
        }
    }
    
    return $errors;
}

// Error response helper
function sendErrorResponse($message, $code = 400, $details = null) {
    http_response_code($code);
    $response = [
        'success' => false,
        'error' => $message
    ];
    
    // Only include details in development
    if (defined('DEBUG') && DEBUG && $details) {
        $response['details'] = $details;
    }
    
    echo json_encode($response);
    exit();
}

// Success response helper
function sendSuccessResponse($data = null, $message = 'Success') {
    $response = [
        'success' => true,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

// Logging helper
function logApiRequest($endpoint, $data, $response_code) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'request_data' => $data,
        'response_code' => $response_code
    ];
    
    error_log('API Request: ' . json_encode($log_entry));
}

// Get client IP
function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}
?>
