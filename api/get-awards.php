<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$db = Database::getInstance();
$year = $_GET['year'] ?? date('Y');
$awardType = $_GET['award'] ?? '';

$sql = "SELECT * FROM awards WHERE year = :year";
$params = ['year' => $year];

if ($awardType) {
    $sql .= " AND award_name = :award";
    $params['award'] = $awardType;
}

$sql .= " ORDER BY award_name, category";

$awards = $db->fetchAll($sql, $params);

jsonResponse($awards);

