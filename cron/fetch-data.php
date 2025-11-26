<?php
/**
 * Cron Job - Fetch and update music/video data
 * Run this daily via cron: 0 0 * * * php /path/to/cron/fetch-data.php
 */

require_once __DIR__ . '/../bootstrap.php';

try {
    $db = Database::getInstance();
    
    // Get all countries, prioritizing African countries
    $countries = $db->fetchAll(
        "SELECT country_code, country_name, is_african, priority 
         FROM countries 
         ORDER BY is_african DESC, priority DESC"
    );
    
    $musicService = new MusicApiService();
    $videoService = new VideoApiService();
    
    echo "Fetching data for " . count($countries) . " countries...\n";
    
    foreach ($countries as $country) {
        echo "Processing {$country['country_name']} ({$country['country_code']})...\n";
        
        try {
            // Fetch music for this country
            $musicService->fetchDataForCountry($country['country_code']);
            echo "  ✓ Music charts fetched\n";
            
            // Fetch videos for this country
            $videoService->fetchDataForCountry($country['country_code']);
            echo "  ✓ Videos fetched\n";
            
            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        } catch (Exception $e) {
            echo "  ✗ Error for {$country['country_name']}: " . $e->getMessage() . "\n";
        }
    }
    
    // Fetch news (global)
    $newsService = new NewsService();
    $newsService->fetchData();
    echo "News articles updated successfully\n";
    
    // Fetch awards (global)
    try {
        $awardsService = new AwardsApiService();
        $awardsService->fetchData();
        echo "Awards data updated successfully\n";
    } catch (Exception $e) {
        echo "Awards fetch error: " . $e->getMessage() . "\n";
    }
    
    // Fetch richest people for major countries
    $majorCountries = ['US', 'ZW', 'ZA', 'NG', 'KE', 'GB', 'CA', 'AU'];
    $richestService = new RichestApiService();
    foreach ($majorCountries as $country) {
        try {
            $richestService->fetchData($country);
            echo "Richest people data for {$country} updated\n";
        } catch (Exception $e) {
            echo "Richest fetch error for {$country}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nAll data fetched and cached successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

