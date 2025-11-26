<?php
/**
 * Base API Service with Caching
 */
abstract class ApiService {
    protected $db;
    protected $cacheDuration;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $settings = new Settings();
        // Get cache duration from settings, default to 3 days
        $cacheDays = (int)$settings->get('cache_duration_days', 3);
        $this->cacheDuration = $cacheDays * 86400; // Convert days to seconds
    }
    
    protected function getCachedData($key) {
        $result = $this->db->fetchOne(
            "SELECT cache_data, expires_at FROM api_cache WHERE cache_key = :key AND expires_at > NOW()",
            ['key' => $key]
        );
        
        if ($result) {
            return json_decode($result['cache_data'], true);
        }
        
        return null;
    }
    
    protected function setCachedData($key, $data) {
        $expiresAt = date('Y-m-d H:i:s', time() + $this->cacheDuration);
        
        $existing = $this->db->fetchOne(
            "SELECT id FROM api_cache WHERE cache_key = :key",
            ['key' => $key]
        );
        
        if ($existing) {
            $this->db->update('api_cache', [
                'cache_data' => json_encode($data),
                'expires_at' => $expiresAt
            ], 'cache_key = :key', ['key' => $key]);
        } else {
            $this->db->insert('api_cache', [
                'cache_key' => $key,
                'cache_data' => json_encode($data),
                'expires_at' => $expiresAt
            ]);
        }
    }
    
    protected function isCacheExpired($key) {
        $result = $this->db->fetchOne(
            "SELECT expires_at FROM api_cache WHERE cache_key = :key",
            ['key' => $key]
        );
        
        if (!$result) {
            return true;
        }
        
        return strtotime($result['expires_at']) < time();
    }
    
    protected function getSetting($key, $default = '') {
        $result = $this->db->fetchOne(
            "SELECT setting_value FROM settings WHERE setting_key = :key",
            ['key' => $key]
        );
        
        return $result ? $result['setting_value'] : $default;
    }
    
    abstract public function fetchData();
    abstract public function processData($rawData);
}

