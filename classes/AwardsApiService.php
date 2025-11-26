<?php
/**
 * Awards API Service - Fetches music awards data
 */
class AwardsApiService extends ApiService {
    private $newsApiKey;
    
    public function __construct() {
        parent::__construct();
        $this->newsApiKey = $this->getSetting('news_api_key');
    }
    
    public function fetchData() {
        $cacheKey = 'awards_data_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        // Fetch from multiple sources
        $rawData = $this->fetchFromApis();
        
        // Process
        $processedData = $this->processData($rawData);
        
        // Store in cache
        $this->setCachedData($cacheKey, $processedData);
        
        // Store in database
        $this->storeInDatabase($processedData);
        
        return $processedData;
    }
    
    private function fetchFromApis() {
        $data = [];
        
        // Try News API for awards news
        if ($this->newsApiKey) {
            try {
                $newsData = $this->fetchAwardsNews();
                if ($newsData) {
                    $data = array_merge($data, $newsData);
                }
            } catch (Exception $e) {
                error_log("Awards News API error: " . $e->getMessage());
            }
        }
        
        // Add Grammy Awards data (structured data)
        $grammyData = $this->getGrammyAwards();
        $data = array_merge($data, $grammyData);
        
        return $data;
    }
    
    private function fetchAwardsNews() {
        $url = "https://newsapi.org/v2/everything?q=music+awards+OR+grammy+OR+mtv+awards&sortBy=popularity&apiKey={$this->newsApiKey}&pageSize=20";
        
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
    
    private function getGrammyAwards() {
        // Structured Grammy Awards data (can be enhanced with real API)
        return [
            [
                'award_name' => 'Grammy Awards',
                'category' => 'Record of the Year',
                'year' => date('Y'),
                'winner' => 'Various Artists',
                'nominees' => ['Artist 1', 'Artist 2', 'Artist 3'],
                'description' => 'The Grammy Award for Record of the Year'
            ],
            [
                'award_name' => 'Grammy Awards',
                'category' => 'Album of the Year',
                'year' => date('Y'),
                'winner' => 'Various Artists',
                'nominees' => ['Artist 1', 'Artist 2', 'Artist 3'],
                'description' => 'The Grammy Award for Album of the Year'
            ],
            [
                'award_name' => 'MTV Video Music Awards',
                'category' => 'Video of the Year',
                'year' => date('Y'),
                'winner' => 'Various Artists',
                'nominees' => ['Artist 1', 'Artist 2', 'Artist 3'],
                'description' => 'MTV VMA for Video of the Year'
            ],
            [
                'award_name' => 'Billboard Music Awards',
                'category' => 'Top Artist',
                'year' => date('Y'),
                'winner' => 'Various Artists',
                'nominees' => ['Artist 1', 'Artist 2', 'Artist 3'],
                'description' => 'Billboard Music Award for Top Artist'
            ]
        ];
    }
    
    public function processData($rawData) {
        $processed = [];
        
        foreach ($rawData as $item) {
            if (isset($item['award_name'])) {
                // Already structured
                $processed[] = $item;
            } else {
                // News article - extract award info
                $title = $item['title'] ?? '';
                if (stripos($title, 'award') !== false || stripos($title, 'grammy') !== false) {
                    $processed[] = [
                        'award_name' => $this->extractAwardName($title),
                        'category' => $this->extractCategory($title),
                        'year' => date('Y'),
                        'winner' => $this->extractWinner($title, $item['description'] ?? ''),
                        'description' => $item['description'] ?? $item['content'] ?? '',
                        'source' => $item['source']['name'] ?? 'News',
                        'url' => $item['url'] ?? ''
                    ];
                }
            }
        }
        
        return $processed;
    }
    
    private function extractAwardName($text) {
        if (stripos($text, 'grammy') !== false) return 'Grammy Awards';
        if (stripos($text, 'mtv') !== false) return 'MTV Video Music Awards';
        if (stripos($text, 'billboard') !== false) return 'Billboard Music Awards';
        if (stripos($text, 'ama') !== false) return 'American Music Awards';
        return 'Music Awards';
    }
    
    private function extractCategory($text) {
        if (stripos($text, 'album') !== false) return 'Album of the Year';
        if (stripos($text, 'record') !== false) return 'Record of the Year';
        if (stripos($text, 'song') !== false) return 'Song of the Year';
        if (stripos($text, 'video') !== false) return 'Video of the Year';
        return 'General';
    }
    
    private function extractWinner($title, $description) {
        // Simple extraction - can be enhanced
        $text = $title . ' ' . $description;
        // Look for common patterns
        if (preg_match('/wins? by ([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)/', $text, $matches)) {
            return $matches[1];
        }
        return 'Various Artists';
    }
    
    private function storeInDatabase($awards) {
        foreach ($awards as $award) {
            // Check if exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM awards WHERE award_name = :name AND category = :cat AND year = :year",
                [
                    'name' => $award['award_name'],
                    'cat' => $award['category'],
                    'year' => $award['year']
                ]
            );
            
            if (!$existing) {
                $this->db->insert('awards', [
                    'award_name' => $award['award_name'],
                    'category' => $award['category'],
                    'year' => $award['year'],
                    'winner' => $award['winner'],
                    'nominees' => json_encode($award['nominees'] ?? []),
                    'description' => $award['description'] ?? '',
                    'source' => $award['source'] ?? '',
                    'source_url' => $award['url'] ?? ''
                ]);
            }
        }
    }
}

