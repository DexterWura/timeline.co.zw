<?php
// Redirect to login if not logged in, otherwise to dashboard
require_once __DIR__ . '/../bootstrap.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    header('Location: /admin/dashboard.php');
} else {
    header('Location: /admin/login.php');
}
exit;

