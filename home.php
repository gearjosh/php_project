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
  require_once 'db_config.php';
  
  // Require login for this page
  require_login();
  
  // Get current user's registration status
  $current_user_registered = false;
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT registered FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_user_registered = $user_data['registered'] ?? false;
  } catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
  }
  
  // Get all registered users
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, name, email, avatar FROM users WHERE registered = true ORDER BY name");
    $stmt->execute();
    $registered_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $registered_users = [];
    $error = "Database error: " . $e->getMessage();
  }
  ?>


  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-4xl">
      <h2 class="text-2xl font-bold mb-6 text-center">Registered pmail Users</h2>
      
      <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <?php if (empty($registered_users)): ?>
        <p class="text-center text-gray-600">No users have registered for emails yet.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($registered_users as $user): ?>
            <a href="send_email.php?to_user_id=<?php echo $user['id']; ?>" class="block">
              <div class="bg-gray-50 p-4 rounded-lg border hover:bg-gray-100 transition-colors cursor-pointer">
                <div class="text-center">
                  <?php if ($user['avatar'] && file_exists($user['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="w-16 h-16 rounded-full mx-auto mb-2 object-cover">
                  <?php else: ?>
                    <div class="w-16 h-16 rounded-full mx-auto mb-2 bg-yellow-200 flex items-center justify-center text-2xl">
                      😊
                    </div>
                  <?php endif; ?>
                  <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($user['name']); ?></h3>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <div class="mt-6 text-center">
        <?php if (!$current_user_registered): ?>
          <a href="register.php" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 inline-block">Register for Emails</a>
        <?php endif; ?>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>