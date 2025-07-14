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
  
  // Get stats data
  // Number of pmails sent
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE sender_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $pmails_sent = $stmt->fetchColumn();
  
  // Number of pmails received
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $pmails_received = $stmt->fetchColumn();
  
  // Number of unique users interacted with
  $stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT user_id) FROM (
      SELECT recipient_id as user_id FROM messages WHERE sender_id = ?
      UNION
      SELECT sender_id as user_id FROM messages WHERE recipient_id = ?
    ) as interactions
  ");
  $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
  $unique_interactions = $stmt->fetchColumn();
  
  // Best friend (user with most interactions)
  $stmt = $pdo->prepare("
    SELECT u.id, u.username, u.name, u.avatar, u.tagline, COUNT(*) as interaction_count
    FROM users u
    INNER JOIN (
      SELECT recipient_id as user_id FROM messages WHERE sender_id = ?
      UNION ALL
      SELECT sender_id as user_id FROM messages WHERE recipient_id = ?
    ) as interactions ON u.id = interactions.user_id
    WHERE u.id != ?
    GROUP BY u.id, u.username, u.name, u.avatar, u.tagline
    ORDER BY interaction_count DESC
    LIMIT 1
  ");
  $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
  $best_friend = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Total number of users to discover
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id != ? AND registered = true");
  $stmt->execute([$_SESSION['user_id']]);
  $total_users = $stmt->fetchColumn();
  $users_to_discover = $total_users - $unique_interactions > 0 ? $total_users - $unique_interactions : 0;
  
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
  } elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {

  }
  ?>


  <div class="flex-grow container mx-auto p-6">
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


    <!-- Profile Header -->
    <div class="bg-white p-8 rounded-lg shadow-md mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-6">
          <div class="flex-shrink-0">
            <?php if ($user_data['avatar'] && file_exists($user_data['avatar'])): ?>
              <img src="<?php echo htmlspecialchars($user_data['avatar']); ?>" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
            <?php else: ?>
              <div class="w-24 h-24 rounded-full bg-yellow-200 flex items-center justify-center text-4xl border-4 border-gray-200">
                😊
              </div>
            <?php endif; ?>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($user_data['username'] ?? ''); ?></h1>
            
            <?php if (!empty($user_data['name'])): ?>
              <span class="text-lg text-gray-600">(<?php echo htmlspecialchars($user_data['name']); ?>)</span>
            <?php endif; ?>

            <?php if (!empty($user_data['tagline'])): ?>
              <p class="text-lg text-gray-600 mt-2"><?php echo htmlspecialchars($user_data['tagline']); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <button onclick="openEditModal()" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 flex items-center space-x-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
          </svg>
          <span>Edit profile</span>
        </button>
      </div>
    </div>


    <!-- Stats Section -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Stats</h2>
      
      <!-- Numbers Subsection -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Numbers</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo isset($pmails_sent) ? $pmails_sent : 'Unknown error. Try again later.'; ?></div>
            <div class="text-sm text-gray-600">pmails sent</div>
          </div>
          <div class="bg-green-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-green-600"><?php echo isset($pmails_received) ? $pmails_received : 'Unknown error. Try again later.'; ?></div>
            <div class="text-sm text-gray-600">pmails received</div>
          </div>
          <div class="bg-purple-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-purple-600"><?php echo isset($unique_interactions) ? $unique_interactions : 'Unknown error. Try again later.'; ?></div>
            <div class="text-sm text-gray-600">users interacted with</div>
          </div>
        </div>
      </div>


      <!-- Best Friend Subsection -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Best Friend</h3>
        <?php if ($best_friend): ?>
          <div class="bg-yellow-50 p-4 rounded-lg flex items-center space-x-4">
            <div class="flex-shrink-0">
              <?php if ($best_friend['avatar'] && file_exists($best_friend['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($best_friend['avatar']); ?>" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
              <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-yellow-200 flex items-center justify-center text-2xl">
                  😊
                </div>
              <?php endif; ?>
            </div>

            <div>
              <div class="font-semibold text-lg"><?php echo isset($best_friend['name']) ? htmlspecialchars($best_friend['name']) : 'Unknown error. Try again later.'; ?></div>
              <?php if (!empty($best_friend['tagline'])): ?>
                <div class="text-gray-600"><?php echo isset($best_friend['tagline']) ? htmlspecialchars($best_friend['tagline']) : 'Unknown error. Try again later.'; ?></div>
              <?php endif; ?>
              <div class="text-sm text-gray-500"><?php echo isset($best_friend['interaction_count']) ? $best_friend['interaction_count'] : 'Unknown error. Try again later.'; ?> interactions</div>
            </div>
          </div>
        <?php else: ?>
          <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-600">
            No interactions yet. Start pmailing someone!
          </div>
        <?php endif; ?>
      </div>

      <!-- Discover Subsection -->
      <div>
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Discover</h3>
        <div class="bg-indigo-50 p-4 rounded-lg text-center">
          <p class="text-gray-700 mb-4">You have <?php echo isset($users_to_discover) ? $users_to_discover : 'Unknown error. Try again later.'; ?> users still to interact with</p>
          <a href="send_email.php?discover=true" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 inline-block">
            pmail someone new
          </a>
        </div>
      </div>
    </div>
  </div>


  <!-- Edit Profile Modal -->
  <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">Update profile for <?php echo htmlspecialchars($user_data['username'] ?? ''); ?></h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <form action="profile.php" method="post" enctype="multipart/form-data" class="flex flex-col">
          <label for="name" class="block text-gray-700 mb-2 custom-form-label">Public Name:</label>
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
          
          <div class="flex space-x-2">
            <button type="submit" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Update Profile</button>
            <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
  <script>
    function openEditModal() {
      document.getElementById('editModal').classList.remove('hidden');
    }
    
    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEditModal();
      }
    });
    
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