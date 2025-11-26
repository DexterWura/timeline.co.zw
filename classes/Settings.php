<?php
/**
 * Settings Manager
 */
class Settings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function get($key, $default = '') {
        $result = $this->db->fetchOne(
            "SELECT setting_value FROM settings WHERE setting_key = :key",
            ['key' => $key]
        );
        
        return $result ? $result['setting_value'] : $default;
    }
    
    public function set($key, $value, $type = 'text', $description = '') {
        $existing = $this->db->fetchOne(
            "SELECT id FROM settings WHERE setting_key = :key",
            ['key' => $key]
        );
        
        if ($existing) {
            $this->db->update('settings', [
                'setting_value' => $value,
                'setting_type' => $type,
                'description' => $description
            ], 'setting_key = :key', ['key' => $key]);
        } else {
            $this->db->insert('settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => $type,
                'description' => $description
            ]);
        }
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM settings ORDER BY setting_key");
    }
    
    public function delete($key) {
        return $this->db->delete('settings', 'setting_key = :key', ['key' => $key]);
    }
}

