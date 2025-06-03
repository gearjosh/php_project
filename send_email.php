<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title></title>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <?php
  session_start(); // Start session to access stored data for repopulation
  $email_data = $_SESSION['email_data'] ?? []; // Retrieve or default to empty
  ?>

  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <p class="text-lg">Hi <?php echo htmlspecialchars($_POST['name'] ?? $email_data['name'] ?? ''); ?>.</p>
    <p>You are <?php echo (int) ($_POST['age'] ?? 0); ?> years old.</p>
    <?php if (($_POST['age'] ?? 0) >= 18): ?>
      <p class="text-green-600 mb-4">You are old enough to enter</p>
      <h2 class="text-xl font-bold mb-4">Send an email to someone</h2>
      <form action="email.php" method="post" class="flex flex-col">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? $email_data['name'] ?? ''); ?>">

        <label for="email" class="block text-gray-700 mb-2 custom-form-label">Your email address:</label>
        <input name="email" id="email" type="email" required class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($email_data['email'] ?? ''); ?>">
        
        <label for="subject" class="block text-gray-700 mb-2 custom-form-label">Your Subject:</label>
        <input name="subject" id="subject" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($email_data['subject'] ?? ''); ?>">
        
        <label for="message" class="block text-gray-700 mb-2 custom-form-label">Your Message:</label>
        <textarea name="message" id="message" class="w-full p-2 border border-gray-300 rounded-md mb-4"><?php echo htmlspecialchars($email_data['message'] ?? ''); ?></textarea>
        
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
      </form>
      <?php
      // Clear session after repopulating to avoid persistent data
      unset($_SESSION['email_data']);
      ?>
    <?php else: ?>
      <p class="text-red-600">You are too young to enter</p>
    <?php endif; ?>
  </div>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>
