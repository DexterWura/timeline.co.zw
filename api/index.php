<?php
// API index - list available endpoints
header('Content-Type: application/json');

$endpoints = [
    'public' => [
        'GET /api/get-charts.php' => 'Get music charts (query: limit, date)',
        'GET /api/get-videos.php' => 'Get video charts (query: limit, date)'
    ],
    'admin' => [
        'POST /api/fetch-music.php' => 'Manually fetch music charts (requires admin auth)',
        'POST /api/fetch-videos.php' => 'Manually fetch videos (requires admin auth)'
    ]
];

echo json_encode($endpoints, JSON_PRETTY_PRINT);

