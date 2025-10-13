<?php
// /config/db.php

// --- IMPORTANT ---
// Fill in your actual database credentials here.
// Do not commit this file with credentials to a public repository.

$db_host = 'localhost'; // Or your database host (e.g., 127.0.0.1)
$db_name = 'etaxi';    // The name of your database
$db_user = 'root';        // Your database username
$db_pass = '';          // Your database password

$charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // In a real application, you would log this error and show a generic message.
    // For now, we'll just show the error.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
