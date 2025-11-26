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
    $musicService = new MusicApiService();
    
    // If force refresh, clear cache and fetch fresh data
    if ($forceRefresh) {
        $db = Database::getInstance();
        // Clear existing cache for today to force refresh
        $db->query("DELETE FROM music_charts WHERE chart_date = CURDATE()");
        // Also clear API cache
        $db->query("DELETE FROM api_cache WHERE cache_key LIKE 'music_charts_%'");
    }
    
    $data = $musicService->fetchData();
    
    if (empty($data)) {
        errorResponse('No music data was fetched. Please check your API keys and try again.', 400);
    }
    
    successResponse($data, 'Music charts fetched and stored successfully. Fetched ' . count($data) . ' items.');
} catch (Exception $e) {
    error_log("fetch-music.php error: " . $e->getMessage());
    errorResponse($e->getMessage(), 500);
}

