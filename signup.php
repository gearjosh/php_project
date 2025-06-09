<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Sign Up</title>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col">
  <?php 
  include 'header.php';
  require_once 'session_utils.php';
  require_once 'db_config.php';
  
  start_secure_session();
  
  // Check if user is already logged in
  if (is_logged_in()) {
    header("Location: home.php");
    exit;
  }
  
  $error = '';
  $success = '';
  
  // Process signup form
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($name)) {
      $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
      $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
      $error = "Password must be at least 6 characters long";
    } else {
      try {
        $pdo = getDBConnection();
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetchColumn()) {
          $error = "Username already exists";
        } else {
          // Check if email already exists
          $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :email");
          $stmt->execute(['email' => $email]);
          if ($stmt->fetchColumn()) {
            $error = "Email already exists";
          } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name) VALUES (:username, :password, :email, :name)");
            $stmt->execute([
              'username' => $username,
              'password' => $hashed_password,
              'email' => $email,
              'name' => $name
            ]);
            
            $success = "Account created successfully! You can now log in.";
          }
        }
      } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
      }
    }
  }
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Create a pmail Account</h2>
      
      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($success); ?>
          <p class="mt-2"><a href="login.php" class="text-blue-500 hover:underline">Go to login page</a></p>
        </div>
      <?php else: ?>
        <form action="signup.php" method="post" class="flex flex-col">
          <label for="username" class="block text-gray-700 mb-2 custom-form-label">Username:</label>
          <input name="username" id="username" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <label for="name" class="block text-gray-700 mb-2 custom-form-label">Full Name:</label>
          <input name="name" id="name" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <label for="email" class="block text-gray-700 mb-2 custom-form-label">Email Address:</label>
          <input name="email" id="email" type="email" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <label for="password" class="block text-gray-700 mb-2 custom-form-label">Password:</label>
          <input name="password" id="password" type="password" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <label for="confirm_password" class="block text-gray-700 mb-2 custom-form-label">Confirm Password:</label>
          <input name="confirm_password" id="confirm_password" type="password" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
          
          <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Sign Up</button>
        </form>
        
        <div class="mt-4 text-center">
          <p>Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Log in</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>