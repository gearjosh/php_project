<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link href="img/favicon.ico" rel="shortcut icon" type="img/jpeg">
  <link href="reset.css" rel="stylesheet" type="text/css" media="all">
  <!-- <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.10/themes/redmond/jquery-ui.css" /> -->
  <link href="styles.css" rel="stylesheet" type="text/css" media="all">
  <title></title>
</head>

<body>

  <p>Hi <?php echo htmlspecialchars($_POST['name']); ?>.</p>
  <p>You are <?php echo (int) $_POST['age']; ?> years old.</p>
  <?php if ($_POST['age'] >= 18): ?>
    <p>You are old enough to enter<p>
        
    <h2>Send an email to someone</h2>
    <form action="email.php" method="post">
      <input type="hidden" name="name" value="<?php echo $_POST['name'] ?>">

      <label for="email">Your email address:</label>
      <input name="email" id="email" type="email" required>
      
      <label for="subject">Your Subject:</label>
      <input name="subject" id="subject" type="text">
      
      <label for="message">Your Message:</label>
      <textarea name="message" id="message">
        
      <button type="submit">Submit</button>
    </form>
  <?php else: ?>
    <p>You are too young to enter<p>
  <?php endif; ?>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script> -->
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>
