<?php
// config/db.php
// Database connection for FitQuest

$DB_HOST = 'localhost';
$DB_USER = 'root';    // default for WAMP
$DB_PASS = '';        // default is empty
$DB_NAME = 'fitquest_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    die("Database connection error: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
?>
