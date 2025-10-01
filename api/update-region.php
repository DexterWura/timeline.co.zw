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
    
    $region = $input['region'] ?? null;
    
    if (!$region) {
        throw new Exception('Region is required');
    }
    
    // Validate region
    $validRegions = ['global', 'africa', 'zimbabwe', 'south-africa', 'nigeria', 'kenya'];
    if (!in_array($region, $validRegions)) {
        throw new Exception('Invalid region');
    }
    
    // Update session
    $_SESSION['user_region'] = $region;
    $_SESSION['region_updated'] = time();
    
    // Log region update (optional)
    error_log("Region updated: {$region}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Region updated successfully',
        'data' => [
            'region' => $region
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
