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

  <form id="login" method="post" action="authentication.php">
    <?php include "menu.php"; ?>
    <fieldset>
    <label for="userID">User ID: </label><input type="text" name="userID" required><br>
    <label for="password">Password: </label><input type="password" name="password" required><br>
    <input type="submit" value="Submit">
    </fieldset>
  </form>

  </div>
</body>
</html>
