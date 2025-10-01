<?php
session_start();
require_once 'secure-config.php';

// Rate limiting
$client_ip = getClientIP();
if (!checkRateLimit($client_ip, 50, 3600)) { // 50 requests per hour
    sendErrorResponse('Rate limit exceeded. Please try again later.', 429);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendErrorResponse('Invalid JSON input');
    }
    
    // Validate input
    $validation_rules = [
        'country' => [
            'required' => true,
            'type' => 'country_code'
        ],
        'region' => [
            'required' => false,
            'type' => 'string',
            'max_length' => 100
        ]
    ];
    
    $validation_errors = validateInput($input, $validation_rules);
    if (!empty($validation_errors)) {
        sendErrorResponse('Validation failed: ' . implode(', ', $validation_errors));
    }
    
    $country = $input['country'];
    $region = $input['region'] ?? null;
    
    // Update session
    $_SESSION['user_country'] = $country;
    $_SESSION['user_region'] = $region;
    $_SESSION['user_language'] = getLanguageForCountry($country);
    $_SESSION['user_timezone'] = getTimezoneForCountry($country);
    $_SESSION['location_updated'] = time();
    
    // Log location update
    logApiRequest('update-location', $input, 200);
    
    sendSuccessResponse([
        'country' => $country,
        'region' => $region,
        'language' => $_SESSION['user_language'],
        'timezone' => $_SESSION['user_timezone']
    ], 'Location updated successfully');
    
} catch (Exception $e) {
    logApiRequest('update-location', $input ?? [], 400);
    sendErrorResponse('An error occurred while updating location', 400, $e->getMessage());
}

function getLanguageForCountry($countryCode) {
    $languages = [
        'ZW' => 'en', 'ZA' => 'en', 'NG' => 'en', 'KE' => 'en', 'GH' => 'en',
        'EG' => 'ar', 'MA' => 'ar', 'TN' => 'ar', 'DZ' => 'ar', 'LY' => 'ar',
        'SD' => 'ar', 'ET' => 'am', 'UG' => 'en', 'TZ' => 'sw', 'RW' => 'rw',
        'BI' => 'rn', 'MW' => 'ny', 'ZM' => 'en', 'BW' => 'en', 'SZ' => 'en',
        'LS' => 'st', 'MZ' => 'pt', 'MG' => 'mg', 'MU' => 'en', 'SC' => 'en',
        'KM' => 'ar', 'DJ' => 'ar', 'SO' => 'so', 'ER' => 'ti', 'SS' => 'en',
        'CF' => 'fr', 'TD' => 'fr', 'NE' => 'fr', 'ML' => 'fr', 'BF' => 'fr',
        'CI' => 'fr', 'LR' => 'en', 'SL' => 'en', 'GN' => 'fr', 'GW' => 'pt',
        'GM' => 'en', 'SN' => 'fr', 'MR' => 'ar', 'CV' => 'pt', 'AO' => 'pt',
        'CD' => 'fr', 'CG' => 'fr', 'GA' => 'fr', 'GQ' => 'es', 'ST' => 'pt',
        'CM' => 'fr', 'TG' => 'fr', 'BJ' => 'fr'
    ];
    
    return $languages[$countryCode] ?? 'en';
}

function getTimezoneForCountry($countryCode) {
    $timezones = [
        'ZW' => 'Africa/Harare', 'ZA' => 'Africa/Johannesburg', 'NG' => 'Africa/Lagos',
        'KE' => 'Africa/Nairobi', 'GH' => 'Africa/Accra', 'EG' => 'Africa/Cairo',
        'MA' => 'Africa/Casablanca', 'TN' => 'Africa/Tunis', 'DZ' => 'Africa/Algiers',
        'LY' => 'Africa/Tripoli', 'SD' => 'Africa/Khartoum', 'ET' => 'Africa/Addis_Ababa',
        'UG' => 'Africa/Kampala', 'TZ' => 'Africa/Dar_es_Salaam', 'RW' => 'Africa/Kigali',
        'BI' => 'Africa/Bujumbura', 'MW' => 'Africa/Blantyre', 'ZM' => 'Africa/Lusaka',
        'BW' => 'Africa/Gaborone', 'SZ' => 'Africa/Mbabane', 'LS' => 'Africa/Maseru',
        'MZ' => 'Africa/Maputo', 'MG' => 'Indian/Antananarivo', 'MU' => 'Indian/Mauritius',
        'SC' => 'Indian/Mahe', 'KM' => 'Indian/Comoro', 'DJ' => 'Africa/Djibouti',
        'SO' => 'Africa/Mogadishu', 'ER' => 'Africa/Asmara', 'SS' => 'Africa/Juba',
        'CF' => 'Africa/Bangui', 'TD' => 'Africa/Ndjamena', 'NE' => 'Africa/Niamey',
        'ML' => 'Africa/Bamako', 'BF' => 'Africa/Ouagadougou', 'CI' => 'Africa/Abidjan',
        'LR' => 'Africa/Monrovia', 'SL' => 'Africa/Freetown', 'GN' => 'Africa/Conakry',
        'GW' => 'Africa/Bissau', 'GM' => 'Africa/Banjul', 'SN' => 'Africa/Dakar',
        'MR' => 'Africa/Nouakchott', 'CV' => 'Atlantic/Cape_Verde', 'AO' => 'Africa/Luanda',
        'CD' => 'Africa/Kinshasa', 'CG' => 'Africa/Brazzaville', 'GA' => 'Africa/Libreville',
        'GQ' => 'Africa/Malabo', 'ST' => 'Africa/Sao_Tome', 'CM' => 'Africa/Douala',
        'TG' => 'Africa/Lome', 'BJ' => 'Africa/Porto-Novo'
    ];
    
    return $timezones[$countryCode] ?? 'Africa/Harare';
}
?>
