<?php
require_once 'db_config.php';


try {
    // Connect to PostgreSQL server without specifying a database
    $dsn = "pgsql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Check if database exists, if not create it
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbname'");
    if ($stmt->fetchColumn() === false) {
        $pdo->exec("CREATE DATABASE $dbname");
        echo "Database created successfully<br>";
    } else {
        echo "Database already exists<br>";
    }
    
    // Connect to the specific database
    $pdo = getDBConnection();
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    echo "Tables created successfully";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>