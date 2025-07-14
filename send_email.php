<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Send Email</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- Header Bar -->
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  require_once 'db_config.php';
  
  // Require login for this page
  require_login();
  
  // Get user data from session
  $user_name = $_SESSION['user_name'] ?? '';
  $user_email = $_SESSION['user_email'] ?? '';
  $email_data = $_SESSION['email_data'] ?? [];
  
  // Get recipient email if user was selected
  $to_address = $_GET['to_address'] ?? '';
  $recipient_name = '';
  $discover = isset($_GET['discover']) && $_GET['discover'] === 'true';

  if (isset($to_address)) {
    try {
      $pdo = getDBConnection();
      $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ? AND registered = true");
      $stmt->execute([$to_address]);
      $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($recipient) {
        $recipient_name = $recipient['name'];
      }
    } catch (PDOException $e) {
      $error = "Database error: " . $e->getMessage();
    }
  }
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Send an email<?php echo !empty($to_address) ? ' to ' . htmlspecialchars($recipient_name ?: $to_address) : ' to ???'; ?></h2>
      
      <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <form action="email.php" method="post" class="flex flex-col">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($user_name); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
        <input type="hidden" name="discover" value="<?php echo htmlspecialchars($discover); ?>">
        <input type="hidden" name="to_address" value="<?php echo htmlspecialchars($to_address); ?>">


        <label for="message_type" class="block text-gray-700 mb-2 custom-form-label">Message Type:</label>
        <select name="message_type" id="message_type" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          <option value="email" <?php echo (($email_data['message_type'] ?? 'email') === 'email') ? 'selected' : ''; ?>>Email</option>
          <option value="pmail" <?php echo (($email_data['message_type'] ?? '') === 'pmail') ? 'selected' : ''; ?>>pmail (Internal Message)</option>
        </select>
        
        <label for="subject" class="block text-gray-700 mb-2 custom-form-label">Your Subject:</label>
        <input name="subject" id="subject" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($email_data['subject'] ?? ''); ?>">
        
        <label for="message" class="block text-gray-700 mb-2 custom-form-label">Your Message:</label>
        <textarea name="message" id="message" required class="w-full p-2 border border-gray-300 rounded-md mb-4"><?php echo htmlspecialchars($email_data['message'] ?? ''); ?></textarea>
        
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
      </form>
      <?php
      // Clear session after repopulating to avoid persistent data
      unset($_SESSION['email_data']);
      ?>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>