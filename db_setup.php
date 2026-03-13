<?php
// Database credentials
$host = 'localhost';
$db   = 'careerexpo';
$user = 'root'; 
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

     // Smažeme tabulky pro čistý import (pořadí je důležité kvůli FK)
     $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
     $tables = ['audit_logs', 'lecture_reservations', 'lectures', 'meetings', 'profile_connections', 'matches', 'candidate_files', 'jobs', 'candidate_profiles', 'company_profiles', 'stands', 'event_registrations', 'events', 'users'];
     foreach ($tables as $table) {
         $pdo->exec("DROP TABLE IF EXISTS $table");
     }
     $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

     $sql = file_get_contents('schema.sql');
     // Odstraníme CREATE DATABASE a USE, protože už jsme připojeni
     $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS careerexpo[^;]*;/', '', $sql);
     $sql = preg_replace('/USE careerexpo;/', '', $sql);

     $pdo->exec($sql);
     echo "Database setup successful with cleaned tables.\n";
} catch (Exception $e) {
     die("Database setup failed: " . $e->getMessage() . "\n");
}

