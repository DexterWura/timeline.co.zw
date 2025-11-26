<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

try {
    $awardsService = new AwardsApiService();
    $data = $awardsService->fetchData();
    successResponse($data, 'Awards data fetched and stored successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

