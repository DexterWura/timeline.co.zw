<?php
// YouTube API Configuration
return [
    'api_key' => $_ENV['YOUTUBE_API_KEY'] ?? 'YOUR_YOUTUBE_API_KEY_HERE',
    'base_url' => 'https://www.googleapis.com/youtube/v3',
    'cache_timeout' => 600, // 10 minutes in seconds
    'max_results' => 50,
    'default_region' => 'ZW', // Zimbabwe
    'default_language' => 'en',
    
    // Regional settings
    'regions' => [
        'global' => [
            'code' => 'US',
            'language' => 'en',
            'category' => '10' // Music
        ],
        'africa' => [
            'code' => 'ZW',
            'language' => 'en',
            'category' => '10' // Music
        ],
        'zimbabwe' => [
            'code' => 'ZW',
            'language' => 'en',
            'category' => '10' // Music
        ],
        'south-africa' => [
            'code' => 'ZA',
            'language' => 'en',
            'category' => '10' // Music
        ],
        'nigeria' => [
            'code' => 'NG',
            'language' => 'en',
            'category' => '10' // Music
        ],
        'kenya' => [
            'code' => 'KE',
            'language' => 'en',
            'category' => '10' // Music
        ]
    ],
    
    // African music genres and search terms
    'african_genres' => [
        'afrobeats' => [
            'search_terms' => ['afrobeats', 'afrobeat', 'nigerian music', 'wizkid', 'burna boy', 'davido'],
            'description' => 'Afrobeats - The modern sound of West Africa'
        ],
        'amapiano' => [
            'search_terms' => ['amapiano', 'south african music', 'piano', 'dance music'],
            'description' => 'Amapiano - South African house music'
        ],
        'zimdancehall' => [
            'search_terms' => ['zimdancehall', 'zimbabwe music', 'dancehall', 'winky d', 'jah prayzah'],
            'description' => 'Zimdancehall - Zimbabwean dancehall music'
        ],
        'chimurenga' => [
            'search_terms' => ['chimurenga', 'thomas mapfumo', 'zimbabwe traditional', 'mbira'],
            'description' => 'Chimurenga - Zimbabwean liberation music'
        ],
        'sungura' => [
            'search_terms' => ['sungura', 'zimbabwe music', 'guitar music', 'oliver mtukudzi'],
            'description' => 'Sungura - Zimbabwean guitar music'
        ],
        'kwaito' => [
            'search_terms' => ['kwaito', 'south african house', 'township music'],
            'description' => 'Kwaito - South African township music'
        ],
        'highlife' => [
            'search_terms' => ['highlife', 'ghana music', 'west african music'],
            'description' => 'Highlife - Ghanaian popular music'
        ],
        'bongo' => [
            'search_terms' => ['bongo flava', 'tanzania music', 'swahili music'],
            'description' => 'Bongo Flava - Tanzanian popular music'
        ]
    ],
    
    // Popular African artists by country
    'african_artists' => [
        'zimbabwe' => [
            'winky d', 'jah prayzah', 'oliver mtukudzi', 'thomas mapfumo', 'sandra ndebele',
            'amara brown', 'tocky vibes', 'freeman', 'sean timba', 'gemma griffiths',
            'tammy moyo', 'roxy music', 'dendera music', 'sulu chimbetu', 'aerosol'
        ],
        'nigeria' => [
            'wizkid', 'burna boy', 'davido', 'tiwa savage', 'yemi alade', 'mr eazi',
            'tekno', 'kizz daniel', 'fireboy dml', 'rema', 'ayra starr', 'asake'
        ],
        'south-africa' => [
            'master kg', 'nomcebo zikode', 'jerusalema', 'amapiano', 'kabza de small',
            'dj maphorisa', 'focalistic', 'semi teejay', 'vigro deep', 'daliwonga'
        ],
        'kenya' => [
            'sauti sol', 'octopizzo', 'khaligraph jones', 'nadia mukami', 'bien',
            'sarah mukami', 'king kaka', 'jua kali', 'nyashinski', 'willy paul'
        ],
        'ghana' => [
            'shatta wale', 'stonebwoy', 'sarkodie', 'shatta wale', 'medikal',
            'kwesi arthur', 'kiDi', 'darkovibes', 'kwame eugene', 'kofi kinaata'
        ]
    ],
    
    // Content filtering
    'content_filters' => [
        'safe_search' => 'moderate',
        'video_definition' => 'any',
        'video_duration' => 'any',
        'video_embeddable' => true,
        'video_license' => 'any',
        'video_syndicated' => 'any'
    ],
    
    // API rate limiting
    'rate_limits' => [
        'requests_per_day' => 10000,
        'requests_per_100_seconds' => 100,
        'requests_per_100_seconds_per_user' => 100
    ]
];
?>
