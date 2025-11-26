<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

$countryCode = $_GET['country'] ?? null;

try {
    $richestService = new RichestApiService();
    $data = $richestService->fetchData($countryCode);
    successResponse($data, 'Richest people data fetched and stored successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

