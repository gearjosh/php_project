<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>PHP Email Project - Success</title>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- Header Bar -->
  <?php include 'header.php'; ?>

  <div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md text-center">
      <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Email Sent Successfully!</h1>
      <p class="text-gray-600 mb-6">Your message has been sent. Thank you for using our service.</p>
      <a href="home.php" class="bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 inline-block">Return Home</a>
    </div>
  </div>
</body>
</html>