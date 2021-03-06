<?php
@session_start(); //start session

//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}

  // Side nav has login button if there's no valid sessions
if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
echo "<nav id='navigation'>
  <ul>
  <li><a href='home.php'>Home</a></li>
  <li><a href='registration.php'>Registration</a></li>
  <li><a href='services.php'>Search Services</a></li>
  <li><a href='login.php'>Login</a></li>
  </ul>
</nav>";
}else{

  // Displays a logout button if the user has a valid session
  echo "<nav id='navigation'>
    <ul>
    <li><a href='home.php'>Home</a></li>
    <li><a href='registration.php'>Registration</a></li>
    <li><a href='services.php'>Search Services</a></li>
    <li><a href='new_task.php'>New Task</a></li>
    <li><a href='tasks.php'>Tasks</a></li>
    <li><a href='profile.php'>Profile</a></li>
    ";

    if($_SESSION['serviceAdmin'] == 1){
      echo "<li><a href='administrator.php'>Admin</a></li>";
    }
    echo "<li><a href='logout.php'>Logout</a></li>
    </ul> </nav>";
}
?>
