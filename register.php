<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Register for Emails</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  require_once 'db_config.php';
  
  // Require login for this page
  require_login();
  ?>


  <div class="flex-grow flex flex-col items-center justify-center p-6">
    <?php
    // Basic form processing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = htmlspecialchars($_POST['name'] ?? '');
      $email = htmlspecialchars($_POST['email'] ?? '');
      $tagline = htmlspecialchars($_POST['tagline'] ?? '');
      
      // Handle avatar upload
      $avatar_path = null;
      if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/avatars/';
        if (!is_dir($upload_dir)) {
          mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar_filename = uniqid() . '.' . $file_extension;
        $avatar_path = $upload_dir . $avatar_filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
          // File uploaded successfully
        } else {
          $avatar_path = null;
        }
      }
      
      // Save to database
      try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE users SET registered = true, name = :name, tagline = :tagline, avatar = :avatar WHERE id = :user_id");
        $stmt->execute([
          'avatar' => $avatar_path,
          'name' => $name,
          'tagline' => $tagline,
          'user_id' => $_SESSION['user_id']
        ]);
        
        $details = '<p class="text-green-600 mb-4">Registration successful!<br/>Name: ' . $name . '<br/>Email: ' . $email . '<br/>Tagline: ' . $tagline . '</p>';
      } catch (PDOException $e) {
        $details = '<p class="text-red-600 mb-4">Registration failed: ' . $e->getMessage() . '</p>';
      }
    }
    ?>
    <?php if($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
      <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <?php echo $details ?>
        <a href="home.php">Back home</a>
      </div>
    <?php } else { ?>
      <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Sign up here to receive pmails from other users!</h2>
        <form action="register.php" method="post" enctype="multipart/form-data" class="flex flex-col">
          <label for="name" class="block text-gray-700 mb-2 custom-form-label">Public Name: <span class="text-sm text-gray-500">This is how you will be known by others</span></label>
          <input name="name" id="name" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
  
          <label for="email" class="block text-gray-700 mb-2 custom-form-label">Email Address:</label>
          <input name="email" id="email" type="email" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
  
          <label for="tagline" class="block text-gray-700 mb-2 custom-form-label">Tagline: <span class="text-sm text-gray-500">(This will be what people see under your name.)</span></label>
          <input name="tagline" id="tagline" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <label for="avatar" class="block text-gray-700 mb-2 custom-form-label">Avatar:</label>
          <input name="avatar" id="avatar" type="file" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md mb-4">
  
          <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Register</button>
        </form>
      </div>
    <?php } ?>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>