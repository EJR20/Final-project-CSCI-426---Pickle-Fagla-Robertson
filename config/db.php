<?php
// config/db.php

// ----- REQUIRED: Aiven MySQL SSL CA -----
// Download the CA certificate from your Aiven service page.
// Save it as: config/ca.pem

$ssl_ca_path = __DIR__ . "/ca.pem";


$db_host = "mysql-2e13c78c-evanjake9393-a852.g.aivencloud.com";
$db_user = "avnadmin";
$db_pass = "AVNS_1QpxYE-_xSTWB9Gz3fR";
$db_name = "defaultdb";   
$db_port = 18588; 

// ----- Establish Secure MySQLi Connection -----
$mysqli = mysqli_init();

// Enable SSL
mysqli_ssl_set($mysqli, NULL, NULL, $ssl_ca_path, NULL, NULL);

// Connect
if (!mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Aiven MySQL connection failed: " . mysqli_connect_error());
}

$mysqli->query("SET SESSION sql_require_primary_key=OFF;");
?>
