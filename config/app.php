<?php
// Application Configuration
return [
    'name' => 'Timeline.co.zw',
    'description' => 'African Music & Entertainment Hub',
    'url' => 'https://timeline.co.zw',
    'version' => '1.0.0',
    
    // YouTube API Configuration
    'youtube' => [
        'api_key' => 'YOUR_YOUTUBE_API_KEY_HERE', // Replace with your actual API key
        'base_url' => 'https://www.googleapis.com/youtube/v3',
        'cache_timeout' => 600, // 10 minutes
        'max_results' => 50
    ],
    
    // Regional Settings
    'default_region' => 'ZW', // Zimbabwe
    'default_language' => 'en',
    
    // African Countries
    'african_countries' => [
        'ZW', 'ZA', 'NG', 'KE', 'GH', 'EG', 'MA', 'TN', 'DZ', 'LY', 'SD', 'ET', 'UG', 'TZ', 'RW', 'BI',
        'MW', 'ZM', 'BW', 'SZ', 'LS', 'MZ', 'MG', 'MU', 'SC', 'KM', 'DJ', 'SO', 'ER', 'SS', 'CF', 'TD',
        'NE', 'ML', 'BF', 'CI', 'LR', 'SL', 'GN', 'GW', 'GM', 'SN', 'MR', 'CV', 'AO', 'CD', 'CG', 'GA',
        'GQ', 'ST', 'CM', 'TG', 'BJ'
    ],
    
    // Cache Settings
    'cache' => [
        'enabled' => true,
        'timeout' => 600, // 10 minutes
        'directory' => 'cache/'
    ],
    
    // Security
    'security' => [
        'session_lifetime' => 3600, // 1 hour
        'csrf_token_lifetime' => 1800, // 30 minutes
        'max_login_attempts' => 5
    ],
    
    // Features
    'features' => [
        'youtube_integration' => true,
        'location_detection' => true,
        'african_content_priority' => true,
        'newsletter' => true,
        'real_time_updates' => true
    ]
];
?>
