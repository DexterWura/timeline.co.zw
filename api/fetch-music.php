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
    $db = Database::getInstance();
    
    // If force refresh, clear cache and fetch fresh data
    if ($forceRefresh) {
        // Clear existing cache for today to force refresh
        $db->query("DELETE FROM music_charts WHERE chart_date = CURDATE()");
        // Also clear API cache for all countries today
        $db->query("DELETE FROM api_cache WHERE cache_key LIKE 'music_charts_%' AND expires_at > NOW()");
    }
    
    $musicService = new MusicApiService();
    
    // Force fetch fresh data (bypass cache check)
    $geo = new Geolocation();
    $countryCode = $geo->detectCountry();
    
    // Directly fetch and store, bypassing cache
    $rawData = $musicService->fetchFromApis();
    
    if (empty($rawData)) {
        errorResponse('No music data was fetched from APIs. Please check your API keys and try again.', 400);
    }
    
    // Process and rank
    $rankedData = $musicService->processData($rawData);
    
    if (empty($rankedData)) {
        errorResponse('No music data was processed. Please check your API response format.', 400);
    }
    
    // Store in database
    $region = $geo->getRegion($countryCode);
    $chartDate = date('Y-m-d');
    
    // Clear old data for today and country
    $db->delete('music_charts', 'chart_date = :date AND country_code = :country', [
        'date' => $chartDate,
        'country' => $countryCode
    ]);
    
    // Store each ranked song
    foreach ($rankedData as $song) {
        $db->insert('music_charts', [
            'rank' => $song['rank'],
            'title' => $song['title'],
            'artist' => $song['artist'],
            'genre' => $musicService->categorizeGenre($song['title'], $song['artist']),
            'streams' => $song['streams'],
            'play_count' => $song['play_count'],
            'artwork_url' => $song['artwork'],
            'chart_date' => $chartDate,
            'country_code' => $countryCode,
            'region' => $region,
            'weeks_on_chart' => 1,
            'peak_position' => $song['rank'],
            'is_new' => 1
        ]);
    }
    
    // Also store in cache for future use
    $cacheKey = 'music_charts_' . $countryCode . '_' . $chartDate;
    
    // Use reflection to access protected method
    $reflection = new ReflectionClass($musicService);
    $setCachedDataMethod = $reflection->getMethod('setCachedData');
    $setCachedDataMethod->setAccessible(true);
    $setCachedDataMethod->invoke($musicService, $cacheKey, $rankedData);
    
    successResponse($rankedData, 'Music charts fetched and stored successfully. Fetched and ranked ' . count($rankedData) . ' items.');
} catch (Exception $e) {
    error_log("fetch-music.php error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    errorResponse($e->getMessage(), 500);
}

