<?php
require __DIR__ . '/vendor/autoload.php';

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$user = $_ENV['POSTGRES_USER'];
$password = $_ENV['POSTGRES_PW'];

$host = 'localhost';
$dbname = 'pmail_db';
$port = '5432'; // Default PostgreSQL port


// Create a PDO connection
function getDBConnection() {
    global $host, $dbname, $user, $password, $port;
    
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>