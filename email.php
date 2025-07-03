<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'db_config.php';


// Looking for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$userName = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];


// Start a session to store form data for success/failure pages
session_start();


// Get recipient details
$to_user_id = null;
$to_email = '';


if (!empty($_POST['to_address'])) {
    $to_email = $_POST['to_address'];
} else {
    // Default to a random registered user's email address
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE registered = true ORDER BY RANDOM() LIMIT 1");
        $stmt->execute();
        $random_user = $stmt->fetch(PDO::FETCH_ASSOC);
        $to_user_id = $random_user['id'] ?? null;
        $to_email = $random_user['email'] ?? "other.josh.gearheart+php@gmail.com";
    } catch (PDOException $e) {
        $to_email = "other.josh.gearheart+php@gmail.com";
    }
}


// Get recipient user ID if not already set
if (!$to_user_id && !empty($to_email)) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND registered = true");
        $stmt->execute([$to_email]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        $to_user_id = $recipient['id'] ?? null;
    } catch (PDOException $e) {
        error_log("Failed to get recipient user ID: " . $e->getMessage());
    }
}


$from = $_POST['email'];
$name = $_POST['name'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$message_type = $_POST['message_type'] ?? 'email';


// Store form data in session for use in success.php or failure.php
$_SESSION['email_data'] = [
  'name' => $name,
  'email' => $from,
  'subject' => $subject,
  'message' => $message,
  'message_type' => $message_type
];


$success = false;


if ($message_type === 'pmail') {
    // Handle pmail (internal message only)
    if ($to_user_id && isset($_SESSION['user_id'])) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO messages (sender_id, recipient_id, to_email, from_email, subject, message, is_email) 
                VALUES (?, ?, ?, ?, ?, ?, false)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $to_user_id,
                $to_email,
                $from,
                $subject,
                $message
            ]);
            $success = true;
        } catch (PDOException $e) {
            error_log("Failed to record pmail in database: " . $e->getMessage());
            $success = false;
        }
    } else {
        error_log("Cannot send pmail: missing user IDs");
        $success = false;
    }
} else {
    // Handle email sending
    // Fix: Define headers properly (uncommented and formatted correctly for mail() compatibility)
    $headers = "From: $name <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


    // Attempt to send the email and log errors for debugging
    if (mail($to_email, $subject, $message, $headers)) {
        // Record the message in the database if we have user IDs
        if ($to_user_id && isset($_SESSION['user_id'])) {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    INSERT INTO messages (sender_id, recipient_id, to_email, from_email, subject, message, is_email) 
                    VALUES (?, ?, ?, ?, ?, ?, true)
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $to_user_id,
                    $to_email,
                    $from,
                    $subject,
                    $message
                ]);
            } catch (PDOException $e) {
                error_log("Failed to record email in database: " . $e->getMessage());
            }
        }
        $success = true;
    } else {
        // Optional: Log the error for debugging (check your server's error log)
        error_log("Email sending failed: To: $to_email, Subject: $subject");
        $success = false;
    }
}


if ($success) {
    header('Location: /success.php');
} else {
    header('Location: /failure.php');
}
?>