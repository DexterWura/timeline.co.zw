<?php
/**
 * Bootstrap file - Loads all required classes and configuration
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $file = CLASSES_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize security
$security = Security::getInstance();
$security->secureHeaders();

// Helper functions
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function errorResponse($message, $statusCode = 400) {
    jsonResponse(['error' => $message], $statusCode);
}

function successResponse($data, $message = '') {
    $response = ['success' => true, 'data' => $data];
    if ($message) {
        $response['message'] = $message;
    }
    jsonResponse($response);
}

