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
  
  // Get current user data to check for existing values
  $pdo = getDBConnection();
  $stmt = $pdo->prepare("SELECT name, tagline, avatar FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Determine if fields should be displayed
  $show_name = empty($user_data['name']);
  $show_tagline = empty($user_data['tagline']);
  $show_avatar = empty($user_data['avatar']) || $user_data['avatar'] === 'default_smiley.png';
  ?>


  <div class="flex-grow flex flex-col items-center justify-center p-6">
    <?php
    // Basic form processing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = htmlspecialchars($_POST['name'] ?? '');
      $tagline = htmlspecialchars($_POST['tagline'] ?? '');
      
      // Handle avatar upload
      $avatar_path = $user_data['avatar'] ?? null;
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
          $avatar_path = $user_data['avatar'] ?? null;
        }
      }
      
      // Prepare update parameters
      $update_params = ['user_id' => $_SESSION['user_id']];
      $update_fields = "registered = true";
      
      if (!empty($name)) {
        $update_fields .= ", name = :name";
        $update_params['name'] = $name;
      }
      if (!empty($tagline)) {
        $update_fields .= ", tagline = :tagline";
        $update_params['tagline'] = $tagline;
      }
      if ($avatar_path !== null && $avatar_path !== ($user_data['avatar'] ?? null)) {
        $update_fields .= ", avatar = :avatar";
        $update_params['avatar'] = $avatar_path;
      }
      
      // Save to database
      try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE users SET $update_fields WHERE id = :user_id");
        $stmt->execute($update_params);
        
        $details = '<p class="text-green-600 mb-4">Registration successful!</p>';
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
        <h2 class="text-xl font-bold mb-4">Register to receive pmails from other users!</h2>
        <form action="register.php" method="post" enctype="multipart/form-data" class="flex flex-col">
          <?php if ($show_name): ?>
            <label for="name" class="block text-gray-700 mb-2 custom-form-label">Public Name: <span class="text-sm text-gray-500">This is how you will be known by others</span></label>
            <input name="name" id="name" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
          <?php endif; ?>
          
          <?php if ($show_tagline): ?>
            <label for="tagline" class="block text-gray-700 mb-2 custom-form-label">Tagline: <span class="text-sm text-gray-500">(This will be what people see under your name.)</span></label>
            <input name="tagline" id="tagline" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4">
          <?php endif; ?>
          
          <?php if ($show_avatar): ?>
            <label for="avatar" class="block text-gray-700 mb-2 custom-form-label">Avatar:</label>
            <input name="avatar" id="avatar" type="file" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md mb-4">
          <?php endif; ?>
          
          <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Register</button>
        </form>
      </div>
    <?php } ?>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>