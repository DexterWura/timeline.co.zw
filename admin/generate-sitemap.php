<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAdmin()) {
    errorResponse('Unauthorized', 401);
}

$security = Security::getInstance();
$input = json_decode(file_get_contents('php://input'), true);

if (!$security->validateCSRFToken($input['csrf_token'] ?? '')) {
    errorResponse('CSRF token validation failed', 403);
}

try {
    $sitemapGenerator = new SitemapGenerator();
    $sitemapGenerator->generate();
    successResponse(['path' => '/sitemap.xml'], 'Sitemap generated successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage(), 500);
}

