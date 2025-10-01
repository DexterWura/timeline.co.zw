<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $language = $input['language'] ?? null;
    
    if (!$language) {
        throw new Exception('Language is required');
    }
    
    // Validate language
    $validLanguages = ['en', 'sn', 'nd', 'fr', 'pt', 'ar', 'sw', 'am', 'rw', 'rn', 'ny', 'st', 'mg', 'so', 'ti', 'rn'];
    if (!in_array($language, $validLanguages)) {
        throw new Exception('Invalid language code');
    }
    
    // Update session
    $_SESSION['user_language'] = $language;
    $_SESSION['language_updated'] = time();
    
    // Log language update (optional)
    error_log("Language updated: {$language}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Language updated successfully',
        'data' => [
            'language' => $language
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
