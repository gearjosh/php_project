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
  
  // Get pmail inbox messages (non-email messages received by current user)
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
      SELECT m.id, m.subject, m.message, m.created_at, u.name as sender_name, u.email as sender_email
      FROM messages m
      INNER JOIN users u ON m.sender_id = u.id
      WHERE m.recipient_id = ? AND (m.is_email = false OR m.is_email IS NULL)
      ORDER BY m.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $inbox_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $inbox_messages = [];
    $error = "Database error: " . $e->getMessage();
  }
  
  // Get all registered users
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, name, email, avatar, tagline FROM users WHERE registered = true ORDER BY name");
    $stmt->execute();
    $registered_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $registered_users = [];
    $error = "Database error: " . $e->getMessage();
  }
  
  // Get users the current user has sent pmails to (non-email messages only)
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
      SELECT DISTINCT u.id, u.name, u.email, u.avatar, u.tagline
      FROM users u
      INNER JOIN messages m ON u.id = m.recipient_id
      WHERE m.sender_id = ? AND m.is_email = false
      ORDER BY u.name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $sent_to_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $sent_to_users = [];
    $error = "Database error: " . $e->getMessage();
  }
  
  // Get users who have sent pmails to the current user (non-email messages only)
  try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
      SELECT DISTINCT u.id, u.name, u.email, u.avatar, u.tagline
      FROM users u
      INNER JOIN messages m ON u.id = m.sender_id
      WHERE m.recipient_id = ? AND m.is_email = false
      ORDER BY u.name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $received_from_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $received_from_users = [];
    $error = "Database error: " . $e->getMessage();
  }
  ?>


  <div class="flex-grow container mx-auto p-6">
    <?php if (isset($error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <!-- pmail Inbox Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <h2 class="text-2xl font-bold mb-6 text-center">pmail Inbox</h2>
      
      <?php if (empty($inbox_messages)): ?>
        <p class="text-center text-gray-600">No pmails received yet.</p>
      <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($inbox_messages as $msg): ?>
            <div class="bg-gray-50 p-4 rounded-lg border">
              <div class="flex justify-between items-start mb-2">
                <div class="flex-grow">
                  <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($msg['subject']); ?></h3>
                  <p class="text-sm text-gray-600">From: <?php echo htmlspecialchars($msg['sender_name']); ?> (<?php echo htmlspecialchars($msg['sender_email']); ?>)</p>
                </div>
                <div class="text-sm text-gray-500">
                  <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                </div>
              </div>
              <div class="text-gray-700">
                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Registered Users Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <h2 class="text-2xl font-bold mb-6 text-center">pmail Anybody!</h2>
      
      <?php if (empty($registered_users)): ?>
        <p class="text-center text-gray-600">No users have registered for emails yet.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($registered_users as $user): ?>
            <a href="send_email.php?to_address=<?php echo $user['email']; ?>" class="block">
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
                  <?php if (!empty($user['tagline'])): ?>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['tagline']); ?></p>
                  <?php endif; ?>

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
    
    <!-- People You've pmailed Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <h2 class="text-2xl font-bold mb-6 text-center">People You've pmailed</h2>
      
      <?php if (empty($sent_to_users)): ?>
        <p class="text-center text-gray-600">You haven't sent any pmails yet.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($sent_to_users as $user): ?>
            <a href="send_email.php?to_address=<?php echo $user['email']; ?>" class="block">
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
                  <?php if (!empty($user['tagline'])): ?>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['tagline']); ?></p>
                  <?php endif; ?>

                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- People Who've pmailed You Section -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6 text-center">People Who've pmailed You</h2>
      
      <?php if (empty($received_from_users)): ?>
        <p class="text-center text-gray-600">No one has sent you a pmail yet.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($received_from_users as $user): ?>
            <a href="send_email.php?to_address=<?php echo $user['email']; ?>" class="block">
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
                  <?php if (!empty($user['tagline'])): ?>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['tagline']); ?></p>
                  <?php endif; ?>

                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
</html>