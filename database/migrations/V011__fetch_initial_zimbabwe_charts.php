<?php
/**
 * Fetch initial music and video charts for Zimbabwe from YouTube API
 * This migration will populate the database with ranked charts (top 100) for Zimbabwe
 */
class V011__fetch_initial_zimbabwe_charts {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Fetch and rank initial music and video charts for Zimbabwe from YouTube API (top 100)';
    }
    
    public function up() {
        try {
            // Check if YouTube API key is configured
            $settings = new Settings();
            $youtubeApiKey = $settings->get('youtube_api_key');
            
            if (empty($youtubeApiKey)) {
                error_log("V011 Migration: YouTube API key not configured. Skipping chart fetch.");
                return; // Don't fail migration if API key is not set
            }
            
            $countryCode = 'ZW'; // Zimbabwe
            $geo = new Geolocation();
            $region = $geo->getRegion($countryCode);
            
            // Initialize services
            $musicService = new MusicApiService();
            $videoService = new VideoApiService();
            
            // Fetch and store music charts for Zimbabwe
            try {
                error_log("V011 Migration: Fetching music charts for Zimbabwe...");
                
                // Clear any existing data for today to ensure fresh fetch
                $chartDate = date('Y-m-d');
                $this->db->delete('music_charts', 'chart_date = :date AND country_code = :country', [
                    'date' => $chartDate,
                    'country' => $countryCode
                ]);
                
                // Clear API cache for music charts
                $this->db->query("DELETE FROM api_cache WHERE cache_key LIKE :key", [
                    'key' => 'music_charts_' . $countryCode . '_%'
                ]);
                
                // Fetch from APIs
                $rawMusicData = $musicService->fetchFromApis();
                
                if (empty($rawMusicData)) {
                    error_log("V011 Migration: No music data fetched from APIs");
                } else {
                    // Process and rank
                    $rankedMusicData = $musicService->processData($rawMusicData);
                    
                    if (!empty($rankedMusicData)) {
                        // Store in database
                        foreach ($rankedMusicData as $song) {
                            $this->db->insert('music_charts', [
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
                        
                        error_log("V011 Migration: Successfully stored " . count($rankedMusicData) . " music charts for Zimbabwe");
                    } else {
                        error_log("V011 Migration: No ranked music data after processing");
                    }
                }
            } catch (Exception $e) {
                error_log("V011 Migration: Error fetching music charts: " . $e->getMessage());
                // Don't fail the migration, just log the error
            }
            
            // Fetch and store video charts for Zimbabwe
            try {
                error_log("V011 Migration: Fetching video charts for Zimbabwe...");
                
                // Clear any existing data for today to ensure fresh fetch
                $this->db->delete('videos', 'chart_date = :date AND country_code = :country', [
                    'date' => $chartDate,
                    'country' => $countryCode
                ]);
                
                // Clear API cache for video charts
                $this->db->query("DELETE FROM api_cache WHERE cache_key LIKE :key", [
                    'key' => 'video_charts_' . $countryCode . '_%'
                ]);
                
                // Use reflection to access private methods for direct fetch (bypassing cache)
                $videoServiceReflection = new ReflectionClass($videoService);
                $fetchFromApisMethod = $videoServiceReflection->getMethod('fetchFromApis');
                $fetchFromApisMethod->setAccessible(true);
                $processDataMethod = $videoServiceReflection->getMethod('processData');
                $processDataMethod->setAccessible(true);
                $storeInDatabaseMethod = $videoServiceReflection->getMethod('storeInDatabase');
                $storeInDatabaseMethod->setAccessible(true);
                
                // Fetch from APIs
                $rawVideoData = $fetchFromApisMethod->invoke($videoService);
                
                if (empty($rawVideoData)) {
                    error_log("V011 Migration: No video data fetched from APIs");
                } else {
                    // Process and rank
                    $rankedVideoData = $processDataMethod->invoke($videoService, $rawVideoData);
                    
                    if (!empty($rankedVideoData)) {
                        // Store in database
                        $storeInDatabaseMethod->invoke($videoService, $rankedVideoData, $countryCode, $region);
                        error_log("V011 Migration: Successfully stored " . count($rankedVideoData) . " video charts for Zimbabwe");
                    } else {
                        error_log("V011 Migration: No ranked video data after processing");
                    }
                }
            } catch (Exception $e) {
                error_log("V011 Migration: Error fetching video charts: " . $e->getMessage());
                // Don't fail the migration, just log the error
            }
            
            error_log("V011 Migration: Completed fetching charts for Zimbabwe");
            
        } catch (Exception $e) {
            error_log("V011 Migration: Fatal error: " . $e->getMessage());
            // Don't throw - allow migration to complete even if API fetch fails
            // The migration itself is successful, just the data fetch might have failed
        }
    }
}

