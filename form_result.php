<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
    <title>Submission Results</title>
</head>
<!-- testing -->
<body>
  <header>
    <h1>KSU Student Services</h1>
  </header>

  <div id="wrapper">
  <nav id="navigation">
    <ul>
    <li><a href="registration.php">Registration</a></li>
    <li><a href="services.php">Search Services</a></li>
    </ul>
  </nav>

  <h3>Submission Results</h3>
  <div id="results">
  <?php include "menu.php"; ?>
  <?php
  // Create blank variables to store our $_POST information
  $netIdErr = $firstNameErr = $lastNameErr = $emailErr = $servicesErr = $timeErr = $daysErr = "";
  $netId = $firstName = $lastName = $email = $services = $time = $days = "";
  // Boolean to make sure everything was entered.
  $confirmation = true;

  // Remove whitespace, backspaces, and convert special characters to HTML entities prior to assigning it to a variable.
  function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
  }

  echo "<br>";

  // If any of the fields are empty, display an error. Otherwise, assign the value after testing the input.
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["netId"])) {
      $netIdErr = "KSU NetID is required";
      echo $netIdErr . "<br>";
      $confirmation = false;
    } else {
      $netId = test_input($_POST["netId"]);
      echo "KSU NetID: " . $netId . "<br>";
    }

    if (empty($_POST["firstName"])) {
      $firstNameErr = "First name is required";
      echo $firstNameErr . "<br>";
      $confirmation = false;
    } else {
      $firstName = test_input($_POST["firstName"]);
      echo "First Name: " . $firstName . "<br>";
    }

    if (empty($_POST["lastName"])) {
      $lastNameErr = "Last name is required";
      echo $lastNameErr . "<br>";
      $confirmation = false;
    } else {
      $lastName = test_input($_POST["lastName"]);
      echo "Last Name: " . $lastName . "<br>";
    }

    if (empty($_POST["email"])) {
      $emailErr = "Email is required";
      echo $emailErr . "<br>";
      $confirmation = false;
    } else {
      $email = test_input($_POST["email"]);
      echo "Email: " . $email . "<br>";
    }

    if(empty($_POST['services'])){
    //read all provided values as an array and join them as comma separated string
      $servicesErr = "No services selected";
      echo $servicesErr  . "<br>";
      $confirmation = false;
    } else {
      $services = implode(",", $_POST['services']);

      echo "Available Services: " . $services  . "<br>";
    }

    if(empty($_POST['days'])){
    //read all provided values as an array and join them as comma separated string
      $daysErr = "No days selected";
      echo $daysErr  . "<br>";
      $confirmation = false;
    } else {
      $days = implode(",", $_POST['days']);
      echo "Available Times: " . $days  . "<br>";
    }

    if(empty($_POST['time'])){
    //read all provided values as an array and join them as comma separated string
      $timeErr = "No times selected";
      echo $timeErr;
      $confirmation = false;
    } else {
      $time = implode(",", $_POST['time']);
      echo "Available Times: " . $time  . "<br>";
    }

    $date = date("m/d/y");
    // Msg that we'll send in an email, assuming everything was filled out.
    $msg = $firstName . " " . $lastName . " (". $email . ") " . "Thanks for registering! " . "\n" . $date . "\n".$netId . "\n" .
      $firstName . " " . $lastName . "\n". $email . "\n". $services . "\n". $days . "\n". $time . "\n";


    // Boolan value to make sure everything was filled out.
    if($confirmation){
      if(!empty($_POST['emailConfirmation'])){
        $emailChk=true;
      }else{
        $emailChk=false;
      }

      // specify database connection credentials
      $conn = new mysqli("localhost", "student_user","my*password", "abrooks");
      // mysqli connect construct from http://php.net/manual/en/mysqli.construct.php
      // very similar to the example used in displayRecords.php
      if (mysqli_connect_error()) {
      die('Connect Error (' . mysqli_connect_errno() . ') '
              . mysqli_connect_error());
      }else{
        $query = mysqli_prepare($conn,
        "INSERT INTO profile (netID, fname, lname, email, post_date, avail_days, avail_hours, confirmation) VALUES(?, ?, ?, ?, ?, ?, ?, ?)")
          or die("Error: ". mysqli_error($conn));
          // bind parameters "s" - string
        mysqli_stmt_bind_param ($query, "ssssssss",$netId, $firstName, $lastName, $email, $date, $days, $time, $emailChk);

        mysqli_stmt_execute($query)
          or die("Error. Could not insert into the table."
            . mysqli_error($conn));

        // mysqli_insert_id($conn) Returns the auto generated id used
        // in the last query for current connection
        $profile_id = mysqli_insert_id($conn);
        mysqli_stmt_close($query);
      }


      // Loop through the selected services so we can retrieve the primary key from services table
      foreach($_POST['services'] as $service) {
        // Query for the primary key located in the services table
        $query = mysqli_prepare($conn,
          "SELECT svcID FROM services WHERE service_description=?")
            or die("Error: ". mysqli_error($conn));
        mysqli_stmt_bind_param ($query, "s", $service);
        mysqli_stmt_execute($query)
          or die("Error. Could not insert into the table." . mysqli_error($conn));

        // Assign results to variable
        $result = mysqli_stmt_get_result($query);

        // Fetch the array and assign it to variable
        $row = mysqli_fetch_array($result);

        // Assign primary key to a variable for our insert statement below
        $svcID = $row[0];
        mysqli_stmt_close($query);

        // Insert the primary key for the services table
        // Insert the primary key for the profile table
        $query = mysqli_prepare($conn,
          "INSERT INTO services_offered (svcID, profileID) VALUES (?,?)")
            or die("Error: ". mysqli_error($conn));

        mysqli_stmt_bind_param($query, "ii",$svcID, $profile_id);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);
      }

      //Send confirmation email if the checkbox was checked.
      if(!empty($_POST['emailConfirmation'])){
        $to = $email;
        $subject = "Registration";
        $body = $msg;
        if (mail($to, $subject, $body)) {
          echo("<p>Confirmation email message successfully sent!</p>");
        } else {
          echo("<p>Confirmation email message delivery failed...</p>");
        }
      } else {
        echo ("<p>No confirmation email was requested...</p>");
      }
      mysqli_close($conn);
      // Display a message if any of the fields are left out.
      } else {
      echo "<p> All fields are required. Press the return button and complete the form. </p>";
      }
    }

    // Close the connection to mysql database

  ?>
  </div>
  </div>
  </body>
</html>
