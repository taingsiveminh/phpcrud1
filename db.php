<?php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'userdb');

$connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Connection Error: " . $connection->connect_error);
}

$connection->set_charset('utf8mb4');
?>