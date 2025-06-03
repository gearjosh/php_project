<?php
  require __DIR__ . '/vendor/autoload.php';

  // Looing for .env at the root directory
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  $userName = $_ENV['USERNAME'];
  $password = $_ENV['PASSWORD'];

// Start a session to store form data for success/failure pages
session_start();

$to = "other.josh.gearheart+php@gmail.com";
$from = $_POST['email'];
$name = $_POST['name'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Fix: Define headers properly (uncommented and formatted correctly for mail() compatibility)
$headers = "From: $name <$from>\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Store form data in session for use in success.php or failure.php
$_SESSION['email_data'] = [
  'name' => $name,
  'email' => $from,
  'subject' => $subject,
  'message' => $message
];

// Attempt to send the email and log errors for debugging
if (mail($to, $subject, $message, $headers)) {
  header('Location: /success.php');
} else {
  // Optional: Log the error for debugging (check your server's error log)
  error_log("Email sending failed: To: $to, Subject: $subject");
  header('Location: /failure.php');
}
