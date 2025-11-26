<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

try {
    $musicService = new MusicApiService();
    $data = $musicService->fetchData();
    successResponse($data, 'Music charts fetched and stored successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

