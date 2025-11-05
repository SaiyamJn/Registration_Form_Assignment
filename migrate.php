<?php
// Read env vars (Render will set these). Provide local XAMPP defaults.
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'registration_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

// Try to connect to the database; if DB doesn't exist, try to create it (if permitted).
$dsnWithDb = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsnWithDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // Connected to DB successfully.
} catch (PDOException $e) {
    // If database doesn't exist, try connecting without a database to create it.
    try {
        $dsnNoDb = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsnNoDb, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Create database if permitted
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        // reconnect with the created database
        $pdo = new PDO($dsnWithDb, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e2) {
        // Could not create DB or connect — show error and exit non-zero so start.sh can retry
        echo "Migration DB connect failed: " . $e2->getMessage() . PHP_EOL;
        exit(1);
    }
}

// Create the registrations table if it doesn't exist
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

try {
    $pdo->exec($sql);
    echo "Migration OK\n";
    exit(0);
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
