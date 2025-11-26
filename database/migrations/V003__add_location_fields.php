<?php
/**
 * Add location/country fields to charts and videos tables
 */
class V003__add_location_fields {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add location fields (country_code, region) to music_charts and videos tables for location-based charts';
    }
    
    public function up() {
        // Add location fields to music_charts
        try {
            $this->db->query("ALTER TABLE music_charts ADD COLUMN country_code VARCHAR(2) DEFAULT 'ZW' AFTER chart_date");
            $this->db->query("ALTER TABLE music_charts ADD COLUMN region VARCHAR(50) DEFAULT 'africa' AFTER country_code");
            $this->db->query("CREATE INDEX idx_country ON music_charts(country_code)");
            $this->db->query("CREATE INDEX idx_region ON music_charts(region)");
            $this->db->query("CREATE INDEX idx_country_date ON music_charts(country_code, chart_date)");
        } catch (Exception $e) {
            // Columns might already exist
        }
        
        // Add location fields to videos
        try {
            $this->db->query("ALTER TABLE videos ADD COLUMN country_code VARCHAR(2) DEFAULT 'ZW' AFTER chart_date");
            $this->db->query("ALTER TABLE videos ADD COLUMN region VARCHAR(50) DEFAULT 'africa' AFTER country_code");
            $this->db->query("CREATE INDEX idx_country ON videos(country_code)");
            $this->db->query("CREATE INDEX idx_region ON videos(region)");
            $this->db->query("CREATE INDEX idx_country_date ON videos(country_code, chart_date)");
        } catch (Exception $e) {
            // Columns might already exist
        }
        
        // Create countries reference table
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS countries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    country_code VARCHAR(2) NOT NULL UNIQUE,
                    country_name VARCHAR(100) NOT NULL,
                    region VARCHAR(50),
                    is_african BOOLEAN DEFAULT 0,
                    priority INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_code (country_code),
                    INDEX idx_region (region)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Insert African countries with high priority
            $africanCountries = [
                ['ZW', 'Zimbabwe', 'africa', 1, 100],
                ['ZA', 'South Africa', 'africa', 1, 95],
                ['KE', 'Kenya', 'africa', 1, 90],
                ['NG', 'Nigeria', 'africa', 1, 90],
                ['GH', 'Ghana', 'africa', 1, 85],
                ['EG', 'Egypt', 'africa', 1, 85],
                ['TZ', 'Tanzania', 'africa', 1, 80],
                ['UG', 'Uganda', 'africa', 1, 80],
                ['ET', 'Ethiopia', 'africa', 1, 75],
                ['AO', 'Angola', 'africa', 1, 75],
                ['MZ', 'Mozambique', 'africa', 1, 70],
                ['MW', 'Malawi', 'africa', 1, 70],
                ['ZM', 'Zambia', 'africa', 1, 70],
                ['BW', 'Botswana', 'africa', 1, 65],
                ['NA', 'Namibia', 'africa', 1, 65],
            ];
            
            foreach ($africanCountries as $country) {
                try {
                    $this->db->insert('countries', [
                        'country_code' => $country[0],
                        'country_name' => $country[1],
                        'region' => $country[2],
                        'is_african' => $country[3],
                        'priority' => $country[4]
                    ]);
                } catch (Exception $e) {
                    // Country might already exist
                }
            }
            
            // Insert other major countries
            $otherCountries = [
                ['US', 'United States', 'north-america', 0, 50],
                ['GB', 'United Kingdom', 'europe', 0, 50],
                ['CA', 'Canada', 'north-america', 0, 45],
                ['AU', 'Australia', 'oceania', 0, 45],
                ['FR', 'France', 'europe', 0, 40],
                ['DE', 'Germany', 'europe', 0, 40],
                ['BR', 'Brazil', 'south-america', 0, 40],
                ['IN', 'India', 'asia', 0, 40],
                ['CN', 'China', 'asia', 0, 40],
                ['JP', 'Japan', 'asia', 0, 35],
            ];
            
            foreach ($otherCountries as $country) {
                try {
                    $this->db->insert('countries', [
                        'country_code' => $country[0],
                        'country_name' => $country[1],
                        'region' => $country[2],
                        'is_african' => $country[3],
                        'priority' => $country[4]
                    ]);
                } catch (Exception $e) {
                    // Country might already exist
                }
            }
        } catch (Exception $e) {
            // Table might already exist
        }
    }
}

