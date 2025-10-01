<?php
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
    
    $email = $input['email'] ?? null;
    
    if (!$email) {
        throw new Exception('Email is required');
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Check if email already exists (you can implement database check here)
    // For now, we'll just log it
    $subscriberData = [
        'email' => $email,
        'subscribed_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // Log subscription (you can save to database instead)
    error_log("Newsletter subscription: " . json_encode($subscriberData));
    
    // Here you would typically:
    // 1. Save to database
    // 2. Send confirmation email
    // 3. Add to email marketing service
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully subscribed to newsletter!',
        'data' => [
            'email' => $email,
            'subscribed_at' => $subscriberData['subscribed_at']
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
