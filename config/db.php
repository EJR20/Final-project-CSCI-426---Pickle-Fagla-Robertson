<?php
// config/db.php
// Database connection for FitQuest (local + Render-compatible)

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'fitquest_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    die("Database connection error: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
?>
