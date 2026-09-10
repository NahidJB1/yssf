<?php
// admin_portal/db_config.php

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
    die("Database connection failed. Please check db_config.php settings.");
}

// Function to create the necessary tables if they don't exist
function createTablesIfNotExist($pdo) {
    // 1. Users Table
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'consultant') NOT NULL DEFAULT 'consultant',
        full_name VARCHAR(255) NULL,
        email VARCHAR(255) NULL,
        phone VARCHAR(50) NULL,
        profile_picture VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sqlUsers);

    // Alter Users table for existing installations
    try { $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NULL"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(50) NULL"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL"); } catch (\PDOException $e) {}

    // 2. Consultations Table (Base table)
    $sqlConsultations = "CREATE TABLE IF NOT EXISTS consultations (
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
    $pdo->exec($sqlConsultations);

    // Alter Consultations table for CRM features (non-destructive)
    try { $pdo->exec("ALTER TABLE consultations ADD COLUMN budget VARCHAR(50) NOT NULL DEFAULT 'N/A'"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE consultations ADD COLUMN status ENUM('pending', 'followed_up') NOT NULL DEFAULT 'pending'"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE consultations ADD COLUMN assigned_to INT NULL DEFAULT NULL"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE consultations ADD COLUMN processing_status VARCHAR(50) NOT NULL DEFAULT 'Pending'"); } catch (\PDOException $e) {}
    
    // Add Foreign Key for assigned_to (Ignore error if it already exists)
    try { 
        $pdo->exec("ALTER TABLE consultations ADD CONSTRAINT fk_consultation_user FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL"); 
    } catch (\PDOException $e) {}

    // 3. Documents Table
    $sqlDocuments = "CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        consultation_id INT NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sqlDocuments);

    // 4. Notes Table
    $sqlNotes = "CREATE TABLE IF NOT EXISTS notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        consultation_id INT NOT NULL,
        user_id INT NOT NULL,
        note_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sqlNotes);

    // 5. Activity Logs Table
    $sqlLogs = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action_description VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sqlLogs);

    // Seed default admin account if users table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $defaultUser = 'ysadmin';
        $defaultPass = password_hash('Ysadmin11!!', PASSWORD_DEFAULT);
        $insertStmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'super_admin')");
        $insertStmt->execute([$defaultUser, $defaultPass]);
    }
}

// Automatically create tables on first run
try {
    createTablesIfNotExist($pdo);
} catch (\PDOException $e) {
    // Ignore error if table creation fails due to permissions, etc.
}
?>
