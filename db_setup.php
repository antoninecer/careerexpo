<?php
// Database credentials
$host = 'localhost';
$db   = 'careerexpo';
$user = 'root'; // Try root first if careerexpo doesn't exist
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $pdo->exec("CREATE DATABASE IF NOT EXISTS careerexpo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
     $pdo->exec("USE careerexpo");
     
     $sql = file_get_contents('schema.sql');
     // Remove potential database creation at the top if it already exists
     $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS careerexpo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;/', '', $sql);
     $sql = preg_replace('/USE careerexpo;/', '', $sql);
     
     $pdo->exec($sql);
     echo "Database setup successful.\n";
} catch (\PDOException $e) {
     die("Database setup failed: " . $e->getMessage() . "\n");
}

