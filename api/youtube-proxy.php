<?php
session_start();
require_once 'secure-config.php';

// Rate limiting for YouTube API calls
$client_ip = getClientIP();
if (!checkRateLimit($client_ip, 20, 3600)) { // 20 requests per hour per IP
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
        'action' => [
            'required' => true,
            'type' => 'string',
            'max_length' => 50
        ],
        'region' => [
            'required' => false,
            'type' => 'country_code'
        ],
        'maxResults' => [
            'required' => false,
            'type' => 'string'
        ]
    ];
    
    $validation_errors = validateInput($input, $validation_rules);
    if (!empty($validation_errors)) {
        sendErrorResponse('Validation failed: ' . implode(', ', $validation_errors));
    }
    
    $action = $input['action'];
    $region = $input['region'] ?? 'ZW';
    $maxResults = min(intval($input['maxResults'] ?? 50), 50); // Limit to 50 results
    
    // Get YouTube API key from environment
    $api_key = $_ENV['YOUTUBE_API_KEY'] ?? getenv('YOUTUBE_API_KEY');
    if (!$api_key || $api_key === 'YOUR_YOUTUBE_API_KEY') {
        sendErrorResponse('YouTube API not configured', 500);
    }
    
    $base_url = 'https://www.googleapis.com/youtube/v3';
    $response_data = null;
    
    switch ($action) {
        case 'trending':
            $response_data = getTrendingVideos($base_url, $api_key, $region, $maxResults);
            break;
        case 'trending_music':
            $response_data = getTrendingMusic($base_url, $api_key, $region, $maxResults);
            break;
        case 'search':
            $query = $input['query'] ?? '';
            if (empty($query)) {
                sendErrorResponse('Search query is required');
            }
            $response_data = searchVideos($base_url, $api_key, $query, $region, $maxResults);
            break;
        default:
            sendErrorResponse('Invalid action');
    }
    
    if ($response_data) {
        logApiRequest('youtube-proxy', $input, 200);
        sendSuccessResponse($response_data, 'Data retrieved successfully');
    } else {
        sendErrorResponse('Failed to retrieve data from YouTube API', 500);
    }
    
} catch (Exception $e) {
    logApiRequest('youtube-proxy', $input ?? [], 500);
    sendErrorResponse('An error occurred while processing the request', 500, $e->getMessage());
}

function getTrendingVideos($base_url, $api_key, $region, $maxResults) {
    $params = [
        'part' => 'snippet,statistics,contentDetails',
        'chart' => 'mostPopular',
        'regionCode' => $region,
        'maxResults' => $maxResults,
        'key' => $api_key
    ];
    
    $url = $base_url . '/videos?' . http_build_query($params);
    return makeYouTubeRequest($url);
}

function getTrendingMusic($base_url, $api_key, $region, $maxResults) {
    $params = [
        'part' => 'snippet,statistics,contentDetails',
        'chart' => 'mostPopular',
        'regionCode' => $region,
        'videoCategoryId' => '10', // Music category
        'maxResults' => $maxResults,
        'key' => $api_key
    ];
    
    $url = $base_url . '/videos?' . http_build_query($params);
    return makeYouTubeRequest($url);
}

function searchVideos($base_url, $api_key, $query, $region, $maxResults) {
    $params = [
        'part' => 'snippet',
        'q' => $query,
        'type' => 'video',
        'regionCode' => $region,
        'maxResults' => $maxResults,
        'key' => $api_key
    ];
    
    $url = $base_url . '/search?' . http_build_query($params);
    $search_results = makeYouTubeRequest($url);
    
    if ($search_results && isset($search_results['items'])) {
        // Get detailed video information
        $video_ids = array_map(function($item) {
            return $item['id']['videoId'];
        }, $search_results['items']);
        
        $video_ids_string = implode(',', $video_ids);
        $details_url = $base_url . '/videos?' . http_build_query([
            'part' => 'snippet,statistics,contentDetails',
            'id' => $video_ids_string,
            'key' => $api_key
        ]);
        
        return makeYouTubeRequest($details_url);
    }
    
    return $search_results;
}

function makeYouTubeRequest($url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 30,
            'header' => [
                'User-Agent: Timeline.co.zw/1.0',
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception('Failed to fetch data from YouTube API');
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response from YouTube API');
    }
    
    if (isset($data['error'])) {
        throw new Exception('YouTube API error: ' . ($data['error']['message'] ?? 'Unknown error'));
    }
    
    return $data;
}
?>
