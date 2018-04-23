<!-- This file returns the results from the submission of registration.php -->

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
  <?php include "side_nav.php"; ?>

  <h3>Submission Results</h3>
  <div id="results">
  <?php include "menu.php"; ?>
  <?php
  // Create blank variables to store our $_POST information
  $netIdErr = $firstNameErr = $lastNameErr = $emailErr = $servicesErr = $timeErr = $daysErr = $passErr = "";
  $netId = $firstName = $lastName = $email = $services = $time = $days = $password = $svcSuggestion = "";
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

    if (empty($_POST["pass"]) || empty($_POST["passConfirm"])) {
      $passErr = "You must enter a password.";
      echo $passErr . "<br>";
      $confirmation = false;
    } else {
      $password = $_POST["pass"];
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

      //new record information from user and pass it to this //script for insertion
      // connect to ldap server
      $ldapconn = ldap_connect("localhost")
      or die("Could not connect to LDAP server.");

      // use OpenDJ version V3 protocol
      if (ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3)){
      } // end if
      else {
         echo "<p>Failed to set version to protocol 3</p>";
      } // end else

      //administrator credentials in order to add new entries
      $ldaprdn = "cn=manager,dc=designstudio1,dc=com";
      $ldappass = "my*password"; // associated password

      if ($ldapconn) {
          // binding to ldap server
          $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

          // verify binding
         if ($ldapbind) {
            //create new record
            $ldaprecord['givenName'] = $firstName;
            $ldaprecord['sn'] = $lastName;
            $ldaprecord['cn'] = $netId;
            $ldaprecord['objectclass'][0] = "top";
            $ldaprecord['objectclass'][1] = "person";
            $ldaprecord['objectclass'][2] = "inetOrgPerson";
            $ldaprecord['userPassword'] = $password;
            $ldaprecord['mail'] = $email;

            //add new record
            if (@ldap_add($ldapconn, "cn=" . $netId .
               ",dc=designstudio1,dc=com", $ldaprecord)){
                $msg = "Thank you <b>" . $firstName . " " .
                   $lastName . "</b> for registering on our" .
                      " website.";
                //display thank you message on the website
                echo $msg;

            } // end if
            else {
                echo "<br>User already exists";
                exit;
            }
         } // end if
         else {
            echo("<p>Failed to register you! (bind error)</p>");
         } // end else
         //close ldap connection VERY IMPORTANT
         ldap_close($ldapconn);
      } //end if
      else {
           echo("<p>Failed to register you! (no ldap server) </p>");
      } //end else

      // Check to see if the user wants an email.
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

        // Insert service suggestion if the field isn't empty
        if(!empty($_POST['svcSuggestion']) || strlen($_POST['svcSuggestion']) >= 3){
          $svcSuggestion = $_POST['svcSuggestion'];
        $query = mysqli_prepare($conn,
        "INSERT INTO service_suggestion (service_description, profileID) VALUES(?,?)")
          or die("Error: ". mysqli_error($conn));
          // bind parameters "s" - string
        mysqli_stmt_bind_param ($query, "si",$svcSuggestion,$profile_id);

        mysqli_stmt_execute($query)
          or die("Error. Could not insert into the table."
            . mysqli_error($conn));
          }
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
