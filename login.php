<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>pmail - Login</title>
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
  
  // Process login form
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
      $error = "Username and password are required";
    } else {
      try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password, name FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
          // Login successful
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['user_name'] = $user['name'];
          
          header("Location: home.php");
          exit;
        } else {
          $error = "Invalid username or password";
        }
      } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
      }
    }
  }
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Login to pmail</h2>
      
      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <form action="login.php" method="post" class="flex flex-col">
        <label for="username" class="block text-gray-700 mb-2 custom-form-label">Username:</label>
        <input name="username" id="username" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
        
        <label for="password" class="block text-gray-700 mb-2 custom-form-label">Password:</label>
        <input name="password" id="password" type="password" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
        
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Login</button>
      </form>
      
      <div class="mt-4 text-center">
        <p>Don't have an account? <a href="signup.php" class="text-blue-500 hover:underline">Sign up</a></p>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>