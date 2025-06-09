<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Form Results</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- Header Bar -->
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  
  // Require login for this page
  require_login();
  
  // Process form data
  $name = $_POST['name'] ?? $_SESSION['user_name'] ?? '';
  $age = $_POST['age'] ?? '';
  $captcha = $_POST['captcha'] ?? '';
  
  // Verify CAPTCHA
  $captcha_valid = ($captcha === '5');
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <?php if (!$captcha_valid): ?>
        <p class="text-red-600 mb-4">Incorrect verification answer. Please go back and try again.</p>
        <a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 inline-block">Go Back</a>
      <?php else: ?>
        <h2 class="text-xl font-bold mb-4">Welcome to your home page!</h2>
        <p class="mb-4">Hello, <?php echo htmlspecialchars($name); ?>!</p>
        <p class="mb-4">Your age: <?php echo htmlspecialchars($age); ?></p>
        <a href="send_email.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 inline-block">Send an Email</a>
      <?php endif; ?>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>