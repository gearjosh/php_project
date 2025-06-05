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
  <!-- Header Bar -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="text-xl font-bold">pmail</div>
      <nav>
        <ul class="flex space-x-6">
          <li><a href="home.php" class="hover:underline">Home</a></li>
          <li><a href="send_email.php" class="hover:underline">Send an Email</a></li>
          <li><a href="login.php" class="hover:underline">Login/Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="flex-grow flex items-center justify-center p-6">
    <form action="home.php" method="post" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <label for="name" class="block text-gray-700 mb-2 custom-form-label">Your name:</label>
      <input name="name" id="name" type="text" required class="w-full p-2 border border-gray-300 rounded-md mb-4">

      <label for="age" class="block text-gray-700 mb-2 custom-form-label">Your age:</label>
      <input name="age" id="age" type="number" required class="w-full p-2 border border-gray-300 rounded-md mb-4">
      
      <!-- CAPTCHA-like verification -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 custom-form-label">Verification:</label>
        <div class="bg-gray-100 p-3 rounded-md mb-2">
          <p>What is 2 + 3? (Enter the number)</p>
        </div>
        <input name="captcha" type="text" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>

      <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
    </form>
  </div>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>