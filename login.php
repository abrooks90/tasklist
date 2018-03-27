<!DOCTYPE html>
<html>
<head>
<title>KSU Student Services | Login</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>
</head>
<body>
  <header>
    <h1>KSU Student Services</h1>
  </header>

  <div id="wrapper">
  <?php include "side_nav.php"; ?>

  <h3>Student Services Login</h3>

<form method="post" action="authentication.php">
  <?php include "menu.php"; ?>
<label>User ID: <input type="text" name="userID" required></label><br>
<label>Password: <input type="text" name="password" required></label><br>
<input type="submit" value="Submit">
</form>

</div>

</body>
</html>
