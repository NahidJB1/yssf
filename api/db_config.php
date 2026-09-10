<?php
// api/db_config.php

$host = 'localhost'; // Usually localhost for Hostinger
$db   = 'u510637182_yssfdb';
$user = 'u510637182_Yssfmysql';
$pass = 'Yssf11!!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, we don't want to show the exact error.
    // For debugging during setup, you can uncomment the next line:
    // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    die(json_encode(["status" => "error", "message" => "Database connection failed. Please check db_config.php settings."]));
}

// Function to create the necessary table if it doesn't exist
function createTableIfNotExists($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS consultations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone_number VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        highest_education VARCHAR(100) NOT NULL,
        result_score VARCHAR(50) NOT NULL,
        passing_year VARCHAR(10) NOT NULL,
        interested_to_study VARCHAR(255) NOT NULL,
        has_passport ENUM('Yes, I do', 'No, I don\'t') NOT NULL,
        budget VARCHAR(50) NOT NULL DEFAULT 'N/A',
        status ENUM('pending', 'followed_up') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    
    // Attempt to add the columns if the table was created previously without them
    try {
        $pdo->exec("ALTER TABLE consultations ADD COLUMN budget VARCHAR(50) NOT NULL DEFAULT 'N/A'");
    } catch (\PDOException $e) {}
    
    try {
        $pdo->exec("ALTER TABLE consultations ADD COLUMN status ENUM('pending', 'followed_up') NOT NULL DEFAULT 'pending'");
    } catch (\PDOException $e) {}
}

// Automatically create table on first run (optional but helpful for setup)
try {
    createTableIfNotExists($pdo);
} catch (\PDOException $e) {
    // Ignore error if table creation fails due to permissions, etc.
}
?>

