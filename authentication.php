<!DOCTYPE html>
<html>
<head>
<title>IT Department</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>
</head>
<body>
	<header>
    <h1>KSU Student Services</h1>
  </header>
	<div id="wrapper">
		<?php include "side_nav.php"; ?>
		<h3>Login Error!</h3>
		<div id="results">
	  <?php include "menu.php"; ?>

  <?php
  @session_start(); //start session

  //see http://phpsec.org/projects/guide/4.html
  if (!isset($_SESSION['authenticated'])) {
      session_regenerate_id();
      $_SESSION['authenticated'] = 0;
  }

  if (!empty($_POST["userID"]) && !empty($_POST["password"])){
  $name = $_POST["userID"];
  $pass = $_POST["password"];

  // using ldap bind
  $ldaprdn  = 'cn=' . $name . ',dc=designstudio1,dc=com';
  $ldappass = $pass; // associated password 'your*password'
  // connect to ldap server
  $ldapconn = ldap_connect("localhost")
      or die("Could not connect to LDAP server.");

  if (ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3))
  {
      echo "";//"Using LDAP v3";
  }else{
      echo "Failed to set version to protocol 3";
  }

  if ($ldapconn) {
      // binding to ldap server
      $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
      // verify binding
      if ($ldapbind) {
        $_SESSION["authenticated"] = 1;
        $_SESSION["user"] = $name;
        header("location:home.php");
      } else {
        $_SESSION["authenticated"] = 0;
          echo "Invalid userID or password. <a href='login.php'>Log in here.</a>";
      }
  }
}else{
    echo "Please enter both fields. <a href='login.php'>Log in here.</a>";
  }
  ?>

</div>
</div>
</body>
</html>
