<!DOCTYPE html>
<html>
<head>
<title>Page Title</title>
</head>
<body>

<?php

$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
// mysqli connect construct from http://php.net/manual/en/mysqli.construct.php
// very similar to the example used in displayRecords.php
if (mysqli_connect_error()) {
die('Connect Error (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
}else{

// Retrieve post from the admin page
$add_delete = $_POST;
foreach ($add_delete as $value) {

  // Loop through the $add_delete array and split on the comma, assign to $test.
  $test = explode(",", $value);
  if($test[1] == "DELETE"){
    // Remove the entry from the service_suggestion table
    $query = mysqli_prepare($conn,
    "DELETE FROM service_suggestion WHERE service_description = ?")
      or die("Error: ". mysqli_error($conn));
      // bind parameters "s" - string
    mysqli_stmt_bind_param ($query, "s",$test[0]);
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table."
        . mysqli_error($conn));
    mysqli_stmt_close($query);
    echo $test[0]. " has been deleted. <br>";

  }elseif ($test[1] == "INSERT") {
    // Insert the new service into the services table
    $query = mysqli_prepare($conn,
    "INSERT INTO services (service_description) VALUES (?)")
      or die("Error: ". mysqli_error($conn));
      // bind parameters "s" - string
    mysqli_stmt_bind_param ($query, "s",$test[0]);
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table."
        . mysqli_error($conn));
    mysqli_stmt_close($query);
    echo $test[0]. " has been added to available services and linked to the requestor's profile. <br>";

    // Insert the svcID and profileID into the services_offered table
    $query = mysqli_prepare($conn,
    "INSERT INTO services_offered (svcID,profileID) VALUES (LAST_INSERT_ID(),(SELECT profileID FROM service_suggestion WHERE service_description = ?))")
      or die("Error: ". mysqli_error($conn));
      // bind parameters "s" - string
    mysqli_stmt_bind_param ($query, "s",$test[0]);
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table."
        . mysqli_error($conn));
    mysqli_stmt_close($query);

    // Remove the entry from the service_suggestion table
    $query = mysqli_prepare($conn,
    "DELETE FROM service_suggestion WHERE service_description = ?")
      or die("Error: ". mysqli_error($conn));
      // bind parameters "s" - string
    mysqli_stmt_bind_param ($query, "s",$test[0]);
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table."
        . mysqli_error($conn));
    mysqli_stmt_close($query);
  }
}
mysqli_close($conn);
}

?>
</body>;
</html>
