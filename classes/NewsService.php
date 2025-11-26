<?php
/**
 * News Service - Fetches news from APIs
 */
class NewsService extends ApiService {
    private $newsApiKey;
    
    public function __construct() {
        parent::__construct();
        $this->newsApiKey = $this->getSetting('news_api_key');
    }
    
    public function fetchData() {
        $cacheKey = 'news_articles_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        $allArticles = [];
        
        // Fetch from paid API if key exists
        if ($this->newsApiKey) {
            try {
                $paidArticles = $this->fetchFromApis();
                $allArticles = array_merge($allArticles, $paidArticles);
            } catch (Exception $e) {
                error_log("Paid News API error: " . $e->getMessage());
            }
        }
        
        // Always fetch from free sources (RSS feeds, free APIs)
        try {
            $freeNewsService = new FreeNewsService();
            $freeArticles = $freeNewsService->fetchData();
            $allArticles = array_merge($allArticles, $freeArticles);
        } catch (Exception $e) {
            error_log("Free News Service error: " . $e->getMessage());
        }
        
        // Process
        $processedData = $this->processData($allArticles);
        
        // Store in cache
        $this->setCachedData($cacheKey, $processedData);
        
        // Store in database (already done by FreeNewsService, but ensure all are stored)
        $this->storeInDatabase($processedData);
        
        return $processedData;
    }
    
    private function fetchFromApis() {
        if (!$this->newsApiKey) {
            return [];
        }
        
        try {
            return $this->fetchNewsApi();
        } catch (Exception $e) {
            error_log("News API error: " . $e->getMessage());
            return [];
        }
    }
    
    private function fetchNewsApi() {
        $url = "https://newsapi.org/v2/top-headlines?category=entertainment&apiKey={$this->newsApiKey}";
        
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
    
    public function processData($rawData) {
        $processed = [];
        
        foreach ($rawData as $article) {
            $processed[] = [
                'title' => $article['title'] ?? 'Untitled',
                'content' => $article['content'] ?? $article['description'] ?? '',
                'excerpt' => $article['description'] ?? '',
                'image_url' => $article['urlToImage'] ?? '',
                'source' => $article['source']['name'] ?? 'Unknown',
                'source_url' => $article['url'] ?? '',
                'published_at' => $article['publishedAt'] ?? date('Y-m-d H:i:s')
            ];
        }
        
        return $processed;
    }
    
    private function storeInDatabase($articles) {
        foreach ($articles as $article) {
            $slug = $this->generateSlug($article['title']);
            
            // Check if exists
            $existing = $this->db->fetchOne("SELECT id FROM news_articles WHERE slug = :slug", ['slug' => $slug]);
            
            if (!$existing) {
                $this->db->insert('news_articles', [
                    'title' => $article['title'],
                    'slug' => $slug,
                    'content' => $article['content'],
                    'excerpt' => $article['excerpt'],
                    'image_url' => $article['image_url'],
                    'source' => $article['source'],
                    'source_url' => $article['source_url'],
                    'published_at' => $article['published_at']
                ]);
            }
        }
    }
    
    private function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}

