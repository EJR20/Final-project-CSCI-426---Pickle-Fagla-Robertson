<?php
// config/db.php

$ssl_ca_path = __DIR__ . "/ca.pem";

// Read values from Render environment variables
$db_host = getenv("DB_HOST");
$db_user = getenv("DB_USER");
$db_pass = getenv("DB_PASS");
$db_name = getenv("DB_NAME");
$db_port = getenv("DB_PORT");

// Debug check (remove later)
// var_dump($db_host, $db_user, $db_name, $db_port);

$mysqli = mysqli_init();

// Enable SSL
mysqli_ssl_set($mysqli, NULL, NULL, $ssl_ca_path, NULL, NULL);

// Connect
if (!mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Aiven MySQL connection failed: " . mysqli_connect_error());
}

$mysqli->query("SET SESSION sql_require_primary_key=OFF;");
?>
