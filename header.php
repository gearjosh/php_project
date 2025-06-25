<!-- header.php -->
<?php
require_once 'session_utils.php';
start_secure_session();
$is_logged_in = is_logged_in();
?>


<header class="bg-white shadow-md">
  <div class="container mx-auto px-6 py-3 flex justify-between items-center">
    <div class="flex items-center">
      <a href="index.php" class="text-xl font-bold text-blue-600 shimmer-link">pmail</a>
    </div>
    <nav>
      <ul class="flex space-x-4">
        <li><a href="profile.php" class="text-gray-700 hover:text-blue-600">Profile</a></li>
        <?php if ($is_logged_in): ?>
          <li><a href="send_email.php" class="text-gray-700 hover:text-blue-600">Send Email</a></li>
          <li><a href="logout.php" class="text-gray-700 hover:text-blue-600">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php" class="text-gray-700 hover:text-blue-600">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>