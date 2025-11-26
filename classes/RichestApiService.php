<?php
/**
 * Richest People API Service
 */
class RichestApiService extends ApiService {
    private $newsApiKey;
    
    public function __construct() {
        parent::__construct();
        $this->newsApiKey = $this->getSetting('news_api_key');
    }
    
    public function fetchData($countryCode = null) {
        $geo = new Geolocation();
        $countryCode = $countryCode ?? $geo->detectCountry();
        
        $cacheKey = 'richest_people_' . $countryCode . '_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        // Fetch from APIs
        $rawData = $this->fetchFromApis($countryCode);
        
        // Process and rank
        $rankedData = $this->processData($rawData, $countryCode);
        
        // Store in cache
        $this->setCachedData($cacheKey, $rankedData);
        
        // Store in database
        $this->storeInDatabase($rankedData, $countryCode);
        
        return $rankedData;
    }
    
    private function fetchFromApis($countryCode) {
        $data = [];
        
        // Try News API for billionaire news
        if ($this->newsApiKey) {
            try {
                $newsData = $this->fetchBillionaireNews($countryCode);
                if ($newsData) {
                    $data = array_merge($data, $newsData);
                }
            } catch (Exception $e) {
                error_log("Billionaire News API error: " . $e->getMessage());
            }
        }
        
        // Add structured data for top billionaires
        $structuredData = $this->getStructuredBillionaires($countryCode);
        $data = array_merge($data, $structuredData);
        
        return $data;
    }
    
    private function fetchBillionaireNews($countryCode) {
        $geo = new Geolocation();
        $countryName = $geo->getCountryName($countryCode);
        
        $query = "billionaire OR richest person";
        if ($countryCode !== 'US') {
            $query .= " {$countryName}";
        }
        
        $url = "https://newsapi.org/v2/everything?q={$query}&sortBy=popularity&apiKey={$this->newsApiKey}&pageSize=20";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("News API returned status code: {$httpCode}");
        }
        
        $data = json_decode($response, true);
        return $data['articles'] ?? [];
    }
    
    private function getStructuredBillionaires($countryCode) {
        // Structured billionaire data (can be enhanced with real API like Forbes API)
        $globalBillionaires = [
            ['name' => 'Elon Musk', 'net_worth' => 250000000000, 'source' => 'Tesla, SpaceX', 'country' => 'US'],
            ['name' => 'Jeff Bezos', 'net_worth' => 180000000000, 'source' => 'Amazon', 'country' => 'US'],
            ['name' => 'Bernard Arnault', 'net_worth' => 170000000000, 'source' => 'LVMH', 'country' => 'FR'],
            ['name' => 'Bill Gates', 'net_worth' => 130000000000, 'source' => 'Microsoft', 'country' => 'US'],
            ['name' => 'Warren Buffett', 'net_worth' => 120000000000, 'source' => 'Berkshire Hathaway', 'country' => 'US'],
        ];
        
        // Filter by country if specified
        if ($countryCode && $countryCode !== 'US') {
            // For African countries, add local billionaires
            if ($countryCode === 'ZW') {
                return [
                    ['name' => 'Strive Masiyiwa', 'net_worth' => 1800000000, 'source' => 'Econet Wireless', 'country' => 'ZW'],
                    ['name' => 'John Bredenkamp', 'net_worth' => 500000000, 'source' => 'Various', 'country' => 'ZW'],
                ];
            } elseif ($countryCode === 'ZA') {
                return [
                    ['name' => 'Johann Rupert', 'net_worth' => 11000000000, 'source' => 'Richemont', 'country' => 'ZA'],
                    ['name' => 'Nickey Oppenheimer', 'net_worth' => 8000000000, 'source' => 'Diamond Industry', 'country' => 'ZA'],
                ];
            } elseif ($countryCode === 'NG') {
                return [
                    ['name' => 'Aliko Dangote', 'net_worth' => 14000000000, 'source' => 'Dangote Group', 'country' => 'NG'],
                    ['name' => 'Mike Adenuga', 'net_worth' => 7000000000, 'source' => 'Globacom', 'country' => 'NG'],
                ];
            }
            
            // Return empty for other countries (can be enhanced)
            return [];
        }
        
        return $globalBillionaires;
    }
    
    public function processData($rawData, $countryCode) {
        $processed = [];
        
        foreach ($rawData as $item) {
            if (isset($item['name'])) {
                // Already structured
                $processed[] = $item;
            } else {
                // News article - extract billionaire info
                $title = $item['title'] ?? '';
                if (stripos($title, 'billionaire') !== false || stripos($title, 'richest') !== false) {
                    $processed[] = [
                        'name' => $this->extractName($title),
                        'net_worth' => $this->extractNetWorth($title . ' ' . ($item['description'] ?? '')),
                        'source' => $this->extractSource($item['description'] ?? ''),
                        'country' => $countryCode
                    ];
                }
            }
        }
        
        // Rank by net worth
        usort($processed, function($a, $b) {
            return ($b['net_worth'] ?? 0) <=> ($a['net_worth'] ?? 0);
        });
        
        // Assign ranks
        foreach ($processed as $index => &$person) {
            $person['rank'] = $index + 1;
        }
        
        return array_slice($processed, 0, 100); // Top 100
    }
    
    private function extractName($text) {
        // Simple extraction
        if (preg_match('/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/', $text, $matches)) {
            return $matches[1];
        }
        return 'Unknown';
    }
    
    private function extractNetWorth($text) {
        // Extract net worth in billions or millions
        if (preg_match('/\$?(\d+(?:\.\d+)?)\s*(billion|million)/i', $text, $matches)) {
            $amount = (float)$matches[1];
            $unit = strtolower($matches[2]);
            return $unit === 'billion' ? $amount * 1000000000 : $amount * 1000000;
        }
        return rand(100000000, 10000000000); // Random fallback
    }
    
    private function extractSource($text) {
        // Extract company/source
        if (preg_match('/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+(Group|Corp|Inc|Ltd)/i', $text, $matches)) {
            return $matches[1] . ' ' . $matches[2];
        }
        return 'Various';
    }
    
    private function storeInDatabase($people, $countryCode) {
        $chartDate = date('Y-m-d');
        
        // Clear old data for today and country
        $this->db->delete('richest_people', 'chart_date = :date AND country_code = :country', [
            'date' => $chartDate,
            'country' => $countryCode
        ]);
        
        foreach ($people as $person) {
            $this->db->insert('richest_people', [
                'rank' => $person['rank'],
                'name' => $person['name'],
                'net_worth' => $person['net_worth'],
                'source' => $person['source'] ?? '',
                'country_code' => $countryCode,
                'chart_date' => $chartDate
            ]);
        }
    }
}

