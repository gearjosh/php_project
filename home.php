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
  <?php include 'header.php'; ?>

  <div class="flex-grow flex items-center justify-center p-6">
    <?php
    session_start(); // Start session to access stored data for repopulation
    $email_data = $_SESSION['email_data'] ?? []; // Retrieve or default to empty
    
    // // Verify CAPTCHA
    // if ($_POST['captcha'] !== '5') {
    //   echo '<div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">';
    //   echo '<p class="text-red-600 mb-4">Incorrect verification answer. Please go back and try again.</p>';
    //   echo '<a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 inline-block">Go Back</a>';
    //   echo '</div>';
    //   exit;
    // }
    
    // Store user name in session for later use
    $_SESSION['user_name'] = $_POST['name'] ?? '';
    ?>

    <p>Welcome to your home page!</p>
  </div>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>