<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Home</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  
  start_secure_session();
  $is_logged_in = is_logged_in();
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <?php if ($is_logged_in): ?>
      <form action="home.php" method="post" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <label for="name" class="block text-gray-700 mb-2 custom-form-label">Your name:</label>
        <input name="name" id="name" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">


        <label for="age" class="block text-gray-700 mb-2 custom-form-label">Your age:</label>
        <input name="age" id="age" type="number" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
        
        <!-- CAPTCHA-like verification -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 custom-form-label">Verification:</label>
          <div class="bg-gray-100 p-3 rounded-md mb-2">
            <p>What is 2 + 3? (Enter the number)</p>
          </div>
          <input name="captcha" type="text" required class="w-full p-2 border border-gray-300 rounded-md">
        </div>


        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
      </form>
    <?php else: ?>
      <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Welcome to pmail</h2>
        <p class="mb-4">You need to be logged in to access this feature.</p>
        <a href="login.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 inline-block">Login</a>
        <p class="mt-4">Don't have an account? <a href="signup.php" class="text-blue-500 hover:underline">Sign up</a></p>
      </div>
    <?php endif; ?>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>