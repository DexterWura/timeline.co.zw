<?php
/**
 * Geolocation Detection Service
 */
class Geolocation {
    private $db;
    private $ip;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->ip = $this->getClientIP();
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    public function detectCountry() {
        // Check session first
        if (isset($_SESSION['user_country'])) {
            return $_SESSION['user_country'];
        }
        
        // Try to get from cookie
        if (isset($_COOKIE['user_country'])) {
            $_SESSION['user_country'] = $_COOKIE['user_country'];
            return $_COOKIE['user_country'];
        }
        
        // Detect from IP
        $country = $this->detectFromIP();
        
        // Store in session and cookie
        $_SESSION['user_country'] = $country;
        setcookie('user_country', $country, time() + (86400 * 30), '/'); // 30 days
        
        return $country;
    }
    
    private function detectFromIP() {
        // Try free IP geolocation services
        $services = [
            'ipapi' => "https://ipapi.co/{$this->ip}/country_code/",
            'ip-api' => "http://ip-api.com/json/{$this->ip}?fields=countryCode",
            'geojs' => "https://get.geojs.io/v1/ip/country/{$this->ip}"
        ];
        
        foreach ($services as $service => $url) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode === 200 && $response) {
                    if ($service === 'ip-api') {
                        $data = json_decode($response, true);
                        if (isset($data['countryCode'])) {
                            return strtoupper($data['countryCode']);
                        }
                    } else {
                        $country = trim($response);
                        if (strlen($country) === 2) {
                            return strtoupper($country);
                        }
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        // Default to Zimbabwe for African focus
        return 'ZW';
    }
    
    public function getCountryName($countryCode) {
        $countries = [
            'ZW' => 'Zimbabwe', 'ZA' => 'South Africa', 'KE' => 'Kenya', 'NG' => 'Nigeria',
            'GH' => 'Ghana', 'EG' => 'Egypt', 'TZ' => 'Tanzania', 'UG' => 'Uganda',
            'ET' => 'Ethiopia', 'AO' => 'Angola', 'MZ' => 'Mozambique', 'MW' => 'Malawi',
            'ZM' => 'Zambia', 'BW' => 'Botswana', 'NA' => 'Namibia', 'LS' => 'Lesotho',
            'SZ' => 'Eswatini', 'US' => 'United States', 'GB' => 'United Kingdom',
            'CA' => 'Canada', 'AU' => 'Australia', 'FR' => 'France', 'DE' => 'Germany',
            'IT' => 'Italy', 'ES' => 'Spain', 'BR' => 'Brazil', 'MX' => 'Mexico',
            'IN' => 'India', 'CN' => 'China', 'JP' => 'Japan', 'KR' => 'South Korea'
        ];
        
        return $countries[strtoupper($countryCode)] ?? 'Unknown';
    }
    
    public function isAfricanCountry($countryCode) {
        $africanCountries = [
            'ZW', 'ZA', 'KE', 'NG', 'GH', 'EG', 'TZ', 'UG', 'ET', 'AO',
            'MZ', 'MW', 'ZM', 'BW', 'NA', 'LS', 'SZ', 'SN', 'CI', 'CM',
            'CD', 'SD', 'MA', 'DZ', 'TN', 'LY', 'SO', 'ER', 'DJ', 'RW',
            'BI', 'TD', 'NE', 'ML', 'BF', 'MR', 'GM', 'GN', 'GW', 'SL',
            'LR', 'TG', 'BJ', 'CF', 'CG', 'GA', 'GQ', 'ST', 'CV', 'KM',
            'MG', 'MU', 'SC', 'KM'
        ];
        
        return in_array(strtoupper($countryCode), $africanCountries);
    }
    
    public function getRegion($countryCode) {
        if ($this->isAfricanCountry($countryCode)) {
            return 'africa';
        }
        
        $regions = [
            'US' => 'north-america', 'CA' => 'north-america', 'MX' => 'north-america',
            'GB' => 'europe', 'FR' => 'europe', 'DE' => 'europe', 'IT' => 'europe',
            'ES' => 'europe', 'BR' => 'south-america', 'AR' => 'south-america',
            'IN' => 'asia', 'CN' => 'asia', 'JP' => 'asia', 'KR' => 'asia',
            'AU' => 'oceania', 'NZ' => 'oceania'
        ];
        
        return $regions[strtoupper($countryCode)] ?? 'global';
    }
}

