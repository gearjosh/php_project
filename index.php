<!DOCTYPE html>
<html>
  
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <script src="https://cdn.tailwindcss.com"></script>
  <title></title>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <form action="send_email.php" method="post" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <label for="name" class="block text-gray-700 mb-2 custom-form-label">Your name:</label>
    <input name="name" id="name" type="text" class="w-full p-2 border border-gray-300 rounded-md mb-4">

    <label for="age" class="block text-gray-700 mb-2 custom-form-label">Your age:</label>
    <input name="age" id="age" type="number" class="w-full p-2 border border-gray-300 rounded-md mb-4">

    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
  </form>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>
