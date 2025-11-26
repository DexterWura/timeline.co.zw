<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

try {
    $videoService = new VideoApiService();
    $data = $videoService->fetchData();
    successResponse($data, 'Videos fetched and stored successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

