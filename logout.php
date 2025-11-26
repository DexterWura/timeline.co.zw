<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new Auth();
$auth->logout();

header('Location: /index.php');
exit;

