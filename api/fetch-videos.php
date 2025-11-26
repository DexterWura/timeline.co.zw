<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

// Check if this is a POST request (from admin settings)
$forceRefresh = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $forceRefresh = isset($input['force_refresh']) && $input['force_refresh'] === true;
}

try {
    $videoService = new VideoApiService();
    
    // If force refresh, clear cache and fetch fresh data
    if ($forceRefresh) {
        $db = Database::getInstance();
        // Clear existing cache for today to force refresh
        $db->query("DELETE FROM videos WHERE chart_date = CURDATE()");
    }
    
    $data = $videoService->fetchData();
    successResponse($data, 'Videos fetched and stored successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

