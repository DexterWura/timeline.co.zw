<?php
require_once __DIR__ . '/../bootstrap.php';

session_start();
header('Content-Type: application/json');

$db = Database::getInstance();
$geo = new Geolocation();

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$date = $_GET['date'] ?? date('Y-m-d');
$countryCode = $_GET['country'] ?? 'US';

$richest = $db->fetchAll(
    "SELECT * FROM richest_people WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC LIMIT :limit",
    ['date' => $date, 'country' => $countryCode, 'limit' => $limit]
);

jsonResponse([
    'richest' => $richest,
    'country' => $countryCode,
    'country_name' => $geo->getCountryName($countryCode),
    'date' => $date
]);

