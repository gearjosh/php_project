<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Profile</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  require_once 'db_config.php';
  
  // Require login for this page
  require_login();
  
  $error = '';
  $success = '';
  
  // Get current user data
  $pdo = getDBConnection();
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Process form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $tagline = $_POST['tagline'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
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
    
    // Validate passwords if provided
    if (!empty($password)) {
      if ($password !== $confirm_password) {
        $error = "Passwords do not match";
      } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
      }
    }
    
    if (empty($error)) {
      // Prepare update parameters
      $update_params = ['user_id' => $_SESSION['user_id']];
      $update_fields = [];
      
      if (!empty($name) && $name !== $user_data['name']) {
        $update_fields[] = "name = :name";
        $update_params['name'] = $name;
      }
      if (!empty($email) && $email !== $user_data['email']) {
        $update_fields[] = "email = :email";
        $update_params['email'] = $email;
      }
      if (!empty($tagline) && $tagline !== $user_data['tagline']) {
        $update_fields[] = "tagline = :tagline";
        $update_params['tagline'] = $tagline;
      }
      if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_fields[] = "password = :password";
        $update_params['password'] = $hashed_password;
      }
      if ($avatar_path !== null && $avatar_path !== $user_data['avatar']) {
        $update_fields[] = "avatar = :avatar";
        $update_params['avatar'] = $avatar_path;
      }
      
      if (!empty($update_fields)) {
        try {
          $pdo = getDBConnection();
          
          // Check for email uniqueness if updating email
          if (isset($update_params['email'])) {
            $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :email AND id != :user_id");
            $stmt->execute([
              'email' => $email,
              'user_id' => $_SESSION['user_id']
            ]);
            if ($stmt->fetchColumn()) {
              $error = "Email already exists";
            }
          }
          
          if (empty($error)) {
            $update_sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :user_id";
            $stmt = $pdo->prepare($update_sql);
            $stmt->execute($update_params);
            
            $success = "Profile updated successfully!";
            
            // Refresh user data after update
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
          }
        } catch (PDOException $e) {
          $error = "Database error: " . $e->getMessage();
        }
      } else {
        $success = "No changes were made to your profile.";
      }
    }
  }
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Update your profile</h2>
      
      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>
      
      <form action="profile.php" method="post" enctype="multipart/form-data" class="flex flex-col">
        <label for="name" class="block text-gray-700 mb-2 custom-form-label">Full Name:</label>
        <input name="name" id="name" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>">
        
        <label for="email" class="block text-gray-700 mb-2 custom-form-label">Email Address:</label>
        <input name="email" id="email" type="email" class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
        
        <label for="tagline" class="block text-gray-700 mb-2 custom-form-label">Tagline:</label>
        <input name="tagline" id="tagline" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4" value="<?php echo htmlspecialchars($user_data['tagline'] ?? ''); ?>">
        
        <label for="avatar" class="block text-gray-700 mb-2 custom-form-label">Avatar:</label>
        <input name="avatar" id="avatar" type="file" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md mb-4">
        <?php if ($user_data['avatar'] && file_exists($user_data['avatar'])): ?>
          <div class="mb-4">
            <p class="text-sm text-gray-600">Current avatar:</p>
            <img src="<?php echo htmlspecialchars($user_data['avatar']); ?>" alt="Current avatar" class="w-16 h-16 rounded-full object-cover">
          </div>
        <?php endif; ?>
        
        <label for="password" class="block text-gray-700 mb-2 custom-form-label">New Password (optional):</label>
        <input name="password" id="password" type="password" class="w-full p-2 border border-gray-300 rounded-md mb-4">
        
        <div id="confirm-password-container" style="display: none;">
          <label for="confirm_password" class="block text-gray-700 mb-2 custom-form-label">Confirm New Password:</label>
          <input name="confirm_password" id="confirm_password" type="password" class="w-full p-2 border border-gray-300 rounded-md mb-4">
        </div>
        
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Update Profile</button>
      </form>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const confirmContainer = document.getElementById('confirm-password-container');
      
      passwordInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
          confirmContainer.style.display = 'block';
        } else {
          confirmContainer.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>