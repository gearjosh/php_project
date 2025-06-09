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
  
  // Require login for this page
  require_login();
  
  // Get user data from session
  $user_name = $_SESSION['user_name'] ?? '';
  $email_data = $_SESSION['email_data'] ?? [];
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Send an email to someone</h2>
      <form action="email.php" method="post" class="flex flex-col">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($user_name); ?>">


        <label for="email" class="block text-gray-700 mb-2 custom-form-label">Your email address:</label>
        <input name="email" id="email" type="email" required class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($email_data['email'] ?? ''); ?>">
        
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