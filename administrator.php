<?php
@session_start(); //start session

//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>KSU Student Services | Homepage</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>
<script>
$(function () {
  $('#administrator').on('submit', function (e) {

    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: 'add_service.php',
      data: $('#administrator').serialize(),
      success: function (data) {
        $("#administration").html(data);
      }
    });
  });
});
</script>
</head>
<body>
  <header>
    <h1>KSU Student Services</h1>
  </header>

<div id="wrapper">
  <?php include "side_nav.php"; ?>
  <h3>Student Services Administration</h3>
  <form id="administrator" method="post" action="" >
    <?php include "menu.php";

    if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1 OR $_SESSION['serviceAdmin'] == 0) {
        echo "ERROR: To use this page you need to be an administrator.";
    }
    else {
    $conn = new mysqli("localhost", "student_user","my*password", "abrooks");

    // Query the database for services in our services table
    $query = mysqli_prepare($conn,
      "SELECT service_description FROM service_suggestion")
        or die("Error: ". mysqli_error($conn));
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table." . mysqli_error($conn));

    // Close the connection and the prepared statement
    $result = mysqli_stmt_get_result($query);

    if(mysqli_num_rows($result) != 0){

    // We're going to display the information as a select menu using PHP to echo HTML
    if (!$result) {
       die("Invalid query: " . mysqli_error($conn));
    } else {
      $i = 0;
      echo "<fieldset id='administration'>";
      while($row = mysqli_fetch_array($result)){
        echo "<label>{$row['service_description']} <input type='radio' name='$i' value='{$row['service_description']},INSERT' checked>Add</input>  <input type='radio' name='$i' value='{$row['service_description']},DELETE'>Delete</input></label><br>";
        $i++;
      }
      echo "
       <input type='submit' value='Submit' onclick='loadResponse()'/>
      </fieldset>";
    }
  }else {
    echo "No service additions to display.";



  }
}

    mysqli_stmt_close($query);
    mysqli_close($conn);

   ?>
<div id="placeholder"></div>
 </form>
</div>

</body>
</html>
