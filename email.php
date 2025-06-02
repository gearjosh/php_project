<?php
if(isset($_POST['submit'])) {
  $to = "comedianseanconnery@gmail.com";
  $from = $_POST['email'];
  $name = $_POST['name'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];
  $headers = "From: " . $name . " at " . $email;

  if (mail($to, $subject, $message, $headers)) {
    $g2k = "Mail Sent";
  } else {
    $g2k = "Failed";
  }
}
?>

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
  <?php
    echo "<h2>$g2k</h2>"
  ?>
  <a href="verify.php">Send another email.</a>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script> -->
  <script type="text/javascript" src="script.js"></script>
</body>
  
</html>
