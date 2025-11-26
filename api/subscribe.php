<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$subscription = new Subscription();
$security = Security::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = trim($input['email'] ?? '');
    $type = $input['type'] ?? 'newsletter';
    $source = $input['source'] ?? 'website';
    
    if (empty($email)) {
        errorResponse('Email is required', 400);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Invalid email address', 400);
    }
    
    $auth = new Auth();
    $userId = $auth->isLoggedIn() ? $auth->getUserId() : null;
    
    $result = $subscription->subscribe($email, $type, $userId, $source);
    
    if ($result['success']) {
        successResponse($result, $result['message']);
    } else {
        errorResponse($result['message'], 400);
    }
} else {
    errorResponse('Method not allowed', 405);
}

