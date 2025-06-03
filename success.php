<!DOCTYPE html>
<html>
  
<head>
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
  session_start(); // Start session to access stored data
  $email_data = $_SESSION['email_data'] ?? []; // Retrieve data or default to empty
  ?>

  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md text-center">
    <h2 class="text-2xl font-bold text-green-600 mb-4">Message Sent Successfully!</h2>
    <p class="mb-4">Here are the details of your email:</p>
    <ul class="text-left mb-6">
      <li><strong>Name:</strong> <?php echo htmlspecialchars($email_data['name'] ?? 'N/A'); ?></li>
      <li><strong>Email:</strong> <?php echo htmlspecialchars($email_data['email'] ?? 'N/A'); ?></li>
      <li><strong>Subject:</strong> <?php echo htmlspecialchars($email_data['subject'] ?? 'N/A'); ?></li>
      <li><strong>Message:</strong> <?php echo htmlspecialchars($email_data['message'] ?? 'N/A'); ?></li>
    </ul>
    <a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Send another message</a>
  </div>

  <?php
  // Clear session data after display to prevent reuse
  unset($_SESSION['email_data']);
  ?>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>
