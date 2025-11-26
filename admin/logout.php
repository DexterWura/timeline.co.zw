<?php
require_once __DIR__ . '/../bootstrap.php';

$auth = new Auth();
$auth->logout();

header('Location: /admin/login.php');
exit;

