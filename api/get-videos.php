<?php
require_once __DIR__ . '/../bootstrap.php';

session_start();
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $geo = new Geolocation();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    if ($limit > 500) $limit = 500; // Max limit
    if ($limit < 1) $limit = 1;
    
    $date = $_GET['date'] ?? date('Y-m-d');
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = date('Y-m-d');
    }
    
    $countryCode = $_GET['country'] ?? $geo->detectCountry();
    // Sanitize country code (2 uppercase letters only)
    $countryCode = preg_replace('/[^A-Z]/', '', strtoupper(substr($countryCode, 0, 2)));
    if (strlen($countryCode) !== 2) {
        $countryCode = $geo->detectCountry();
    }
    
    $videos = $db->fetchAll(
        "SELECT * FROM videos WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC LIMIT :limit",
        ['date' => $date, 'country' => $countryCode, 'limit' => $limit]
    );
    
    // If no videos for country, get global
    if (empty($videos)) {
        $videos = $db->fetchAll(
            "SELECT * FROM videos WHERE chart_date = :date ORDER BY rank ASC LIMIT :limit",
            ['date' => $date, 'limit' => $limit]
        );
    }
    
    jsonResponse([
        'videos' => $videos,
        'country' => $countryCode,
        'country_name' => $geo->getCountryName($countryCode),
        'date' => $date
    ]);
} catch (Exception $e) {
    error_log("API Error (get-videos): " . $e->getMessage());
    errorResponse('An error occurred while fetching videos', 500);
}

