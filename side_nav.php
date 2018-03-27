<?php
@session_start(); //start session

//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}

if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
echo "<nav id='navigation'>
  <ul>
  <li><a href='home.php'>Home</a></li>
  <li><a href='registration.php'>Registration</a></li>
  <li><a href='services.php'>Search Services</a></li>
  <li><a href='Login.php'>Login</a></li>
  </ul>
</nav>";
}else{
  echo "<nav id='navigation'>
    <ul>
    <li><a href='home.php'>Home</a></li>
    <li><a href='registration.php'>Registration</a></li>
    <li><a href='services.php'>Search Services</a></li>
    <li><a href='transfer_tasks.php'>Task Transfer</a></li>
    <li><a href='logout.php'>Logout</a></li>
    </ul>
  </nav>";
}
?>
