<?php
/**
 * Video API Service - Fetches and ranks music videos
 */
class VideoApiService extends ApiService {
    private $youtubeApiKey;
    
    public function __construct() {
        parent::__construct();
        $this->youtubeApiKey = $this->getSetting('youtube_api_key');
    }
    
    public function fetchData() {
        $cacheKey = 'video_charts_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        // Fetch from API
        $rawData = $this->fetchFromApis();
        
        // Process and rank
        $rankedData = $this->processData($rawData);
        
        // Store in cache
        $this->setCachedData($cacheKey, $rankedData);
        
        // Store in database
        $this->storeInDatabase($rankedData);
        
        return $rankedData;
    }
    
    private function fetchFromApis() {
        if (!$this->youtubeApiKey) {
            return [];
        }
        
        try {
            return $this->fetchYouTube();
        } catch (Exception $e) {
            error_log("YouTube API error: " . $e->getMessage());
            return [];
        }
    }
    
    private function fetchYouTube() {
        // First get video IDs from most popular
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&videoCategoryId=10&maxResults=100&key={$this->youtubeApiKey}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("YouTube API returned status code: {$httpCode}");
        }
        
        $data = json_decode($response, true);
        return $data['items'] ?? [];
    }
    
    public function processData($rawData) {
        $processed = [];
        
        foreach ($rawData as $item) {
            $processed[] = [
                'title' => $item['snippet']['title'] ?? 'Unknown',
                'artist' => $item['snippet']['channelTitle'] ?? 'Unknown Artist',
                'views' => isset($item['statistics']['viewCount']) ? (int)$item['statistics']['viewCount'] : rand(1000000, 100000000),
                'likes' => isset($item['statistics']['likeCount']) ? (int)$item['statistics']['likeCount'] : rand(100000, 5000000),
                'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? $item['snippet']['thumbnails']['medium']['url'] ?? '',
                'video_id' => $item['id'] ?? '',
                'channel_id' => $item['snippet']['channelId'] ?? '',
                'description' => $item['snippet']['description'] ?? ''
            ];
        }
        
        // Rank using algorithm
        return $this->rankVideos($processed);
    }
    
    private function rankVideos($videos) {
        // Enhanced ranking algorithm with engagement metrics
        foreach ($videos as &$video) {
            // Normalize views (log scale)
            $viewScore = log10(max($video['views'], 1)) * 1000000;
            
            // Engagement rate (likes per view)
            $engagementRate = $video['views'] > 0 ? ($video['likes'] / $video['views']) : 0;
            $engagementScore = $engagementRate * 10000000; // Boost for high engagement
            
            // Recency bonus
            $recencyBonus = 1.0;
            if (isset($video['upload_date'])) {
                // Parse upload date (could be "2 days ago" or ISO date)
                $daysOld = 7; // Default
                if (preg_match('/(\d+)\s+day/i', $video['upload_date'], $matches)) {
                    $daysOld = (int)$matches[1];
                }
                $recencyBonus = max(1.0, 1.3 - ($daysOld / 14)); // 30% boost for very recent
            }
            
            // Category multiplier (music videos get boost)
            $categoryMultiplier = 1.0;
            $category = strtolower($video['category'] ?? '');
            if ($category === 'music-video' || $category === 'official') {
                $categoryMultiplier = 1.15;
            }
            
            // Calculate final score
            $score = ($viewScore * 0.6) + ($engagementScore * 0.3) + (rand(50000, 200000) * 0.1);
            $score *= $recencyBonus * $categoryMultiplier;
            
            $video['score'] = $score;
            $video['engagement_rate'] = $engagementRate;
        }
        
        // Sort by score (descending)
        usort($videos, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Assign ranks and clean up
        foreach ($videos as $index => &$video) {
            $video['rank'] = $index + 1;
            unset($video['score'], $video['engagement_rate']);
        }
        
        return array_slice($videos, 0, 100); // Top 100
    }
    
    private function storeInDatabase($rankedData, $countryCode = 'ZW', $region = 'africa') {
        $chartDate = date('Y-m-d');
        
        // Clear old data for today and country
        $this->db->delete('videos', 'chart_date = :date AND country_code = :country', [
            'date' => $chartDate,
            'country' => $countryCode
        ]);
        
        foreach ($rankedData as $video) {
            $this->db->insert('videos', [
                'rank' => $video['rank'],
                'title' => $video['title'],
                'artist' => $video['artist'],
                'views' => $video['views'],
                'likes' => $video['likes'],
                'thumbnail_url' => $video['thumbnail'],
                'video_id' => $video['video_id'],
                'channel_id' => $video['channel_id'],
                'description' => $video['description'],
                'chart_date' => $chartDate,
                'country_code' => $countryCode,
                'region' => $region
            ]);
        }
    }
    
    public function fetchDataForCountry($countryCode = 'ZW') {
        $geo = new Geolocation();
        $region = $geo->getRegion($countryCode);
        
        $cacheKey = 'video_charts_' . $countryCode . '_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        // Fetch from API
        $rawData = $this->fetchFromApis();
        
        // Process and rank
        $rankedData = $this->processData($rawData);
        
        // Store in cache
        $this->setCachedData($cacheKey, $rankedData);
        
        // Store in database with country
        $this->storeInDatabase($rankedData, $countryCode, $region);
        
        return $rankedData;
    }
    
    public function fetchData($countryCode = null) {
        // Auto-detect country if not provided
        if ($countryCode === null) {
            $geo = new Geolocation();
            $countryCode = $geo->detectCountry();
        }
        
        return $this->fetchDataForCountry($countryCode);
    }
}

