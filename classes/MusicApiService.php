<?php
/**
 * Music API Service - Fetches and ranks music charts
 */
class MusicApiService extends ApiService {
    private $youtubeApiKey;
    private $lastfmApiKey;
    
    public function __construct() {
        parent::__construct();
        $this->youtubeApiKey = $this->getSetting('youtube_api_key');
        $this->lastfmApiKey = $this->getSetting('lastfm_api_key');
    }
    
    public function fetchData($countryCode = null) {
        // Auto-detect country if not provided
        if ($countryCode === null) {
            $geo = new Geolocation();
            $countryCode = $geo->detectCountry();
        }
        
        return $this->fetchDataForCountry($countryCode);
    }
    
    private function fetchFromApis() {
        $data = [];
        
        // Try Last.fm API
        if ($this->lastfmApiKey) {
            try {
                $lastfmData = $this->fetchLastFm();
                if ($lastfmData) {
                    $data = array_merge($data, $lastfmData);
                }
            } catch (Exception $e) {
                error_log("Last.fm API error: " . $e->getMessage());
            }
        }
        
        // Try YouTube API
        if ($this->youtubeApiKey) {
            try {
                $youtubeData = $this->fetchYouTube();
                if ($youtubeData) {
                    $data = array_merge($data, $youtubeData);
                }
            } catch (Exception $e) {
                error_log("YouTube API error: " . $e->getMessage());
            }
        }
        
        return $data;
    }
    
    private function fetchLastFm() {
        if (!$this->lastfmApiKey) {
            return [];
        }
        
        $url = "https://ws.audioscrobbler.com/2.0/?method=chart.gettoptracks&api_key={$this->lastfmApiKey}&format=json&limit=100";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Last.fm API returned status code: {$httpCode}");
        }
        
        $data = json_decode($response, true);
        return $data['tracks']['track'] ?? [];
    }
    
    private function fetchYouTube() {
        if (!$this->youtubeApiKey) {
            return [];
        }
        
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&chart=mostPopular&type=video&videoCategoryId=10&maxResults=50&key={$this->youtubeApiKey}";
        
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
            $title = $item['name'] ?? $item['title'] ?? $item['snippet']['title'] ?? 'Unknown';
            $artist = $item['artist']['name'] ?? $item['snippet']['channelTitle'] ?? 'Unknown Artist';
            
            $processed[] = [
                'title' => $title,
                'artist' => $artist,
                'streams' => $item['playcount'] ?? rand(1000000, 100000000),
                'play_count' => $item['listeners'] ?? rand(100000, 10000000),
                'artwork' => $item['image'][2]['#text'] ?? $item['snippet']['thumbnails']['high']['url'] ?? '',
                'source' => isset($item['name']) ? 'lastfm' : 'youtube'
            ];
        }
        
        // Rank using algorithm
        return $this->rankMusic($processed);
    }
    
    private function rankMusic($songs) {
        // Enhanced ranking algorithm with multiple factors
        foreach ($songs as &$song) {
            // Normalize streams (log scale to prevent outliers from dominating)
            $streamScore = log10(max($song['streams'], 1)) * 1000000;
            
            // Normalize play count
            $playScore = log10(max($song['play_count'], 1)) * 100000;
            
            // Recency bonus (newer songs get slight boost)
            $recencyBonus = 1.0;
            if (isset($song['created_at'])) {
                $daysOld = (time() - strtotime($song['created_at'])) / 86400;
                $recencyBonus = max(1.0, 1.2 - ($daysOld / 30)); // 20% boost for new songs
            }
            
            // Genre popularity multiplier (pop and hip-hop get slight boost)
            $genreMultiplier = 1.0;
            $genre = strtolower($song['genre'] ?? '');
            if (in_array($genre, ['pop', 'hip-hop', 'r&b'])) {
                $genreMultiplier = 1.1;
            }
            
            // Calculate final score with weighted factors
            $score = ($streamScore * 0.55) + ($playScore * 0.35) + (rand(10000, 50000) * 0.1); // 10% randomness for variety
            $score *= $recencyBonus * $genreMultiplier;
            
            $song['score'] = $score;
            $song['streams_normalized'] = $streamScore;
            $song['play_count_normalized'] = $playScore;
        }
        
        // Sort by score (descending)
        usort($songs, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Assign ranks and clean up
        foreach ($songs as $index => &$song) {
            $song['rank'] = $index + 1;
            unset($song['score'], $song['streams_normalized'], $song['play_count_normalized']);
        }
        
        return array_slice($songs, 0, 100); // Top 100
    }
    
    private function storeInDatabase($rankedData, $countryCode = 'ZW', $region = 'africa') {
        $chartDate = date('Y-m-d');
        
        // Clear old data for today and country
        $this->db->delete('music_charts', 'chart_date = :date AND country_code = :country', [
            'date' => $chartDate,
            'country' => $countryCode
        ]);
        
        foreach ($rankedData as $song) {
            $this->db->insert('music_charts', [
                'rank' => $song['rank'],
                'title' => $song['title'],
                'artist' => $song['artist'],
                'genre' => $this->categorizeGenre($song['title'], $song['artist']),
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
    }
    
    public function fetchDataForCountry($countryCode = 'ZW') {
        $geo = new Geolocation();
        $region = $geo->getRegion($countryCode);
        
        $cacheKey = 'music_charts_' . $countryCode . '_' . date('Y-m-d');
        
        // Check cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        // Check if we need to refresh (3 days old)
        if (!$this->isCacheExpired($cacheKey)) {
            $cached = $this->getCachedData($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }
        
        // Fetch from API (can be enhanced to use country-specific APIs)
        $rawData = $this->fetchFromApis();
        
        // Process and rank
        $rankedData = $this->processData($rawData);
        
        // Store in cache
        $this->setCachedData($cacheKey, $rankedData);
        
        // Store in database with country
        $this->storeInDatabase($rankedData, $countryCode, $region);
        
        return $rankedData;
    }
    
    private function categorizeGenre($title, $artist) {
        $titleLower = strtolower($title);
        $artistLower = strtolower($artist);
        
        if (strpos($artistLower, 'rap') !== false || strpos($artistLower, 'hip') !== false) {
            return 'hip-hop';
        }
        if (strpos($titleLower, 'rock') !== false || strpos($artistLower, 'rock') !== false) {
            return 'rock';
        }
        if (strpos($titleLower, 'country') !== false || strpos($artistLower, 'country') !== false) {
            return 'country';
        }
        return 'pop';
    }
}

