<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$db = Database::getInstance();
$countryCode = $_GET['country'] ?? null;

$sql = "SELECT * FROM hall_of_fame";
$params = [];

if ($countryCode) {
    $sql .= " WHERE country_code = :country OR country_code IS NULL";
    $params['country'] = $countryCode;
}

$sql .= " ORDER BY year_inducted DESC, artist_name ASC";

$hallOfFame = $db->fetchAll($sql, $params);

jsonResponse($hallOfFame);

