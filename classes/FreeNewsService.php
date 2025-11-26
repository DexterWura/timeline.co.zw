<?php
/**
 * Free News Service - Uses free APIs and RSS feeds (no API key required)
 */
class FreeNewsService extends ApiService {
    
    public function fetchData() {
        $cacheKey = 'free_news_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null && !$this->isCacheExpired($cacheKey)) {
            return $cached;
        }
        
        // Fetch from multiple free sources
        $articles = [];
        
        // Fetch from RSS feeds (free, no API key)
        $rssFeeds = [
            'https://rss.cnn.com/rss/edition.rss',
            'https://feeds.bbci.co.uk/news/entertainment_and_arts/rss.xml',
            'https://www.rollingstone.com/feed/',
            'https://www.billboard.com/feed/',
        ];
        
        foreach ($rssFeeds as $feedUrl) {
            try {
                $feedArticles = $this->fetchRSSFeed($feedUrl);
                $articles = array_merge($articles, $feedArticles);
            } catch (Exception $e) {
                error_log("RSS feed error ({$feedUrl}): " . $e->getMessage());
            }
        }
        
        // Fetch from free news APIs (no key required)
        try {
            $freeApiArticles = $this->fetchFreeNewsAPI();
            $articles = array_merge($articles, $freeApiArticles);
        } catch (Exception $e) {
            error_log("Free News API error: " . $e->getMessage());
        }
        
        // Process and store
        $processed = $this->processData($articles);
        $this->setCachedData($cacheKey, $processed);
        $this->storeInDatabase($processed);
        
        return $processed;
    }
    
    private function fetchRSSFeed($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $xml = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$xml) {
            throw new Exception("RSS feed returned status code: {$httpCode}");
        }
        
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        
        if ($feed === false) {
            throw new Exception("Failed to parse RSS feed");
        }
        
        $articles = [];
        foreach ($feed->channel->item as $item) {
            $articles[] = [
                'title' => (string)$item->title,
                'description' => (string)$item->description,
                'content' => (string)$item->description,
                'url' => (string)$item->link,
                'published_at' => date('Y-m-d H:i:s', strtotime((string)$item->pubDate)),
                'source' => (string)$feed->channel->title,
                'image_url' => $this->extractImageFromRSS($item)
            ];
        }
        
        return $articles;
    }
    
    private function extractImageFromRSS($item) {
        // Try to get image from media:content or enclosure
        if (isset($item->children('media', true)->content)) {
            $media = $item->children('media', true)->content;
            if (isset($media->attributes()->url)) {
                return (string)$media->attributes()->url;
            }
        }
        
        // Try enclosure
        if (isset($item->enclosure) && strpos($item->enclosure['type'], 'image') !== false) {
            return (string)$item->enclosure['url'];
        }
        
        // Extract from description HTML
        if (isset($item->description)) {
            preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', (string)$item->description, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
        }
        
        return '';
    }
    
    private function fetchFreeNewsAPI() {
        // Use free news aggregator APIs (no key required)
        $articles = [];
        
        // NewsAPI free tier (limited but works)
        // Note: This would require API key, so we'll skip it
        
        // Alternative: Use public RSS aggregators
        // For now, return empty - RSS feeds are the main free source
        return $articles;
    }
    
    public function processData($rawData) {
        $processed = [];
        
        foreach ($rawData as $article) {
            $processed[] = [
                'title' => $article['title'] ?? 'Untitled',
                'content' => $article['content'] ?? $article['description'] ?? '',
                'excerpt' => $this->generateExcerpt($article['description'] ?? $article['content'] ?? ''),
                'image_url' => $article['image_url'] ?? '',
                'source' => $article['source'] ?? 'Unknown',
                'source_url' => $article['url'] ?? '',
                'published_at' => $article['published_at'] ?? date('Y-m-d H:i:s'),
                'category' => $this->categorizeArticle($article['title'] ?? '', $article['description'] ?? ''),
                'is_from_api' => true // Mark as API-sourced
            ];
        }
        
        return $processed;
    }
    
    private function generateExcerpt($text, $length = 150) {
        $excerpt = strip_tags($text);
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length) . '...';
        }
        return $excerpt;
    }
    
    private function categorizeArticle($title, $description) {
        $text = strtolower($title . ' ' . $description);
        
        if (strpos($text, 'award') !== false || strpos($text, 'grammy') !== false) {
            return 'Awards';
        }
        if (strpos($text, 'album') !== false || strpos($text, 'release') !== false) {
            return 'Releases';
        }
        if (strpos($text, 'concert') !== false || strpos($text, 'tour') !== false) {
            return 'Concerts';
        }
        if (strpos($text, 'chart') !== false || strpos($text, 'billboard') !== false) {
            return 'Charts';
        }
        
        return 'General';
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
                    'category' => $article['category'],
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

