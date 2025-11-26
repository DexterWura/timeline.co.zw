<?php
/**
 * Fetch music/video data for all countries (especially African countries)
 * Run this via cron to populate charts for all countries
 */
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

$db = Database::getInstance();
$geo = new Geolocation();

// Get all countries, prioritizing African countries
$countries = $db->fetchAll(
    "SELECT country_code, country_name, is_african, priority 
     FROM countries 
     ORDER BY is_african DESC, priority DESC"
);

$results = [];
$musicService = new MusicApiService();
$videoService = new VideoApiService();

foreach ($countries as $country) {
    try {
        // Fetch music for this country
        $musicData = $musicService->fetchDataForCountry($country['country_code']);
        
        // Fetch videos for this country
        $videoData = $videoService->fetchDataForCountry($country['country_code']);
        
        $results[] = [
            'country' => $country['country_code'],
            'country_name' => $country['country_name'],
            'music_count' => count($musicData),
            'video_count' => count($videoData),
            'status' => 'success'
        ];
        
        // Small delay to avoid rate limiting
        usleep(500000); // 0.5 seconds
    } catch (Exception $e) {
        $results[] = [
            'country' => $country['country_code'],
            'country_name' => $country['country_name'],
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
}

successResponse($results, 'Fetched data for ' . count($results) . ' countries');

