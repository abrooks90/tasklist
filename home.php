<!DOCTYPE html>
<html>
<head>
<title>KSU Student Services | Homepage</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>
</head>
<body>
  <header>
    <h1>KSU Student Services</h1>
  </header>

<div id="wrapper">
  <?php include "side_nav.php"; ?>
  <h3>Student Services Home</h3>
  <div id="home">
    <?php include "menu.php"; ?>

    <p><h4>IT6203 - Task Project - Group 6</h4></p>
    <p>
      <h5>Group Members:</h5>
      Andrew Brooks<br>
      Michael Farris<br>
      Joshua Hutchins<br>
      Mohamed Nedjar
    </p>

    <p>
      Welcome to the group project for group 6 of IT6203.
      <br>This site allows a user to register to provide services, or search for available services.
      <br>Upon registration a user may create tasks for available users, transfer tasks to other users, <br> or mark tasks as complete.
      The back-end is a combination of Javascript/AJAX/JQUERY, PHP,<br> and logins are authenticated against the OpenDJ LDAP authentication server.
    </p>

    <p><a href='registration.php'>Click here to begin!</a>
  </p>


  </div>
</div>

</body>
</html>
