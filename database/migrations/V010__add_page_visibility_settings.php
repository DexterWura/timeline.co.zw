<?php
/**
 * Add page visibility settings for controlling which pages are enabled/disabled
 */
class V010__add_page_visibility_settings {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add page visibility settings to control which pages are enabled/disabled (richest, business, blog, awards, news)';
    }
    
    public function up() {
        // Default page visibility settings
        $defaultPages = [
            'richest' => 1,
            'business' => 1,
            'blog' => 1,
            'awards' => 1,
            'news' => 1
        ];
        
        foreach ($defaultPages as $pageKey => $isEnabled) {
            try {
                // Check if setting already exists
                $existing = $this->db->fetchOne(
                    "SELECT id FROM settings WHERE setting_key = :key",
                    ['key' => 'page_enabled_' . $pageKey]
                );
                
                if (!$existing) {
                    $this->db->insert('settings', [
                        'setting_key' => 'page_enabled_' . $pageKey,
                        'setting_value' => $isEnabled ? '1' : '0',
                        'setting_type' => 'boolean',
                        'description' => 'Enable/disable ' . ucfirst($pageKey) . ' page'
                    ]);
                }
            } catch (Exception $e) {
                // Setting might already exist, skip
            }
        }
    }
}

