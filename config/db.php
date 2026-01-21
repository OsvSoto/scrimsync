<?php
// Ubicación: config/db.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scrimsync";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Error fatal de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

define('BASE_URL', 'http://scrimsync.local/');
?>