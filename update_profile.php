<?php
@session_start(); //start session
//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}
?>
<!-- This file returns the results from the update of profile.php -->
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
		<title>KSU Student Services | Profile</title>
	</head>
	<body>
		<header>
			<h1>KSU Student Services</h1>
		</header>

		<div id="wrapper">
			<?php //includes the navigation bar
				include "side_nav.php"; 
			?>

			<h3>Profile Update</h3>
			<div id="results">
				<?php //includes the menu page
					include "menu.php"; 
					
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
	
					// If any of the fields are empty, display an error. Otherwise, assign the value after testing the input.
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						if (empty($_POST["netId"])) {
							$netIdErr = "KSU NetID is required";
							echo $netIdErr . "<br>";
							$confirmation = false;
						} else {
							$netId = test_input($_POST["netId"]);
							echo "<div>KSU NetID: " . $netId . "</div>";
						}
						
						// Msg that we'll send in an email, assuming everything was filled out.
						$msg = $netId . ": Your profile has been updated.<br> The following is your updated information: <br>";
						
						if (empty($_POST["firstName"])) {
							$firstNameErr = "First name is required";
							echo $firstNameErr . "<br>";
							$confirmation = false;
						} else {
							$firstName = test_input($_POST["firstName"]);
							echo "<div>First Name: " . $firstName . "</div>";
							$msg .= "First Name: " . $firstName . "<br>";
						}

						if (empty($_POST["lastName"])) {
							$lastNameErr = "Last name is required";
							echo $lastNameErr . "<br>";
							$confirmation = false;
						} else {
							$lastName = test_input($_POST["lastName"]);
							echo "<div>Last Name: " . $lastName . "</div>";
							$msg .= "Last Name: " . $lastName . "<br>";
						}

						if (empty($_POST["email"])) {
							$emailErr = "Email is required";
							echo $emailErr . "<br>";
							$confirmation = false;
						} else {
							$email = test_input($_POST["email"]);
							echo "<div>Email: " . $email . "</div>";
							$msg .= "Email: " . $email . "<br>";
						}

						if(empty($_POST['services'])){
							//read all provided values as an array and join them as comma separated string
							$servicesErr = "No services selected";
							echo $servicesErr  . "<br>";
							$confirmation = false;
						}

						if(empty($_POST['days'])){
							//read all provided values as an array and join them as comma separated string
							$daysErr = "No days selected";
							echo $daysErr  . "<br>";
							$confirmation = false;
						} else {
							$days = implode(",", $_POST['days']);
							echo "<div>Available Times: " . $days  . "</div>";
							$msg .= "Available Times: " . $days . "<br>";
						}

						if(empty($_POST['time'])){
							//read all provided values as an array and join them as comma separated string
							$timeErr = "No times selected";
							echo $timeErr;
							$confirmation = false;
						} else {
							$time = implode(",", $_POST['time']);
							echo "<div>Available Times: " . $time  . "</div>";
							$msg .= "Available Times: " . $time . "<br>";
						}
						
						//gets the profileID of the user for updating tables
						$profileID = $_POST['profileID'];
						
						//gets the users email address on file for checking if the email changed
						$oldEmail = $_POST['oldEmail'];
						
						//gets the current date for time stamping
						$date = date("m/d/y");
						
						// specify database connection credentials
						$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
						
						// mysqli connect construct from http://php.net/manual/en/mysqli.construct.php
						if (mysqli_connect_error()) {
							die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
						}else{
							//updates the profile information associated with the users profile
							$query = mysqli_prepare($conn,"UPDATE profile SET fname = ?, lname = ?, email = ?, post_date = ?, avail_days = ?, avail_hours = ? WHERE profileID = ?")
							  or die("Error: ". mysqli_error($conn));
							// bind parameters "s" - string "i" - integer
							mysqli_stmt_bind_param ($query, "ssssssi",$firstName, $lastName, $email, $date, $days, $time, $profileID);
							mysqli_stmt_execute($query)
							  or die("Error. Could not insert into the table." . mysqli_error($conn));
							mysqli_stmt_close($query);
						}
						
						//remove previous services from services_offered, in case service is no longer offered
						$query = mysqli_prepare($conn,"DELETE FROM services_offered WHERE profileID = ?");
						mysqli_stmt_bind_param($query,"i",$profileID);
						mysqli_stmt_execute($query)
							or die("Error: " . mysqli_error($conn));
						mysqli_stmt_close($query);
						
						
						// Loop through the svcIDs to get the description for the message
						$services = array();
						foreach($_POST['services'] as $service) {
							// Query for the service description
							$query = mysqli_prepare($conn, "SELECT service_description FROM services WHERE svcID=?")
								or die("Error: ". mysqli_error($conn));
							mysqli_stmt_bind_param ($query, "i", $service);
							mysqli_stmt_execute($query)
								or die("Error. Could not insert into the table." . mysqli_error($conn));
							// Assign results to variable
							$result = mysqli_stmt_get_result($query);
							// Fetch the array and assign it to variable
							$row = mysqli_fetch_array($result);

							// Assign primary key to a variable for our insert statement below
							array_push($services, $row[0]);
							mysqli_stmt_close($query);
						
							// Insert the primary key for the services table
							// Insert the primary key for the profile table
							$query = mysqli_prepare($conn, "INSERT INTO services_offered (svcID, profileID) VALUES (?,?)")
								or die("Error: ". mysqli_error($conn));
							mysqli_stmt_bind_param($query, "ii",$service, $profileID);
							mysqli_stmt_execute($query);
							mysqli_stmt_close($query);
						}
						
						$msg .= "Services Offered: " . implode(",",$services);
						 
						//Send email to user confirming profile update
						$to = $email;
						$subject = "Updated Profile";
						$body = $msg;
						if (mail($to, $subject, $body)) {
							echo "<p>Your profile was successfully updated! An email has been sent to the entered email address.</p>";
						} else {
							echo "<p>Confirmation email message delivery failed...</p>";
						}
						
						//if the email address was changed, send email to old email for security
						if($email != $oldEmail){
							$to = $oldEmail;
							$subject = "Updated Email Address";
							$body = "The email address for user " . $netId . " was changed from this address. If you initiated this change, no action is necessary. Otherwise, please notify a site administrator.";
							if(mail($to,$subject,$body)){
								echo "<p>An email has also been sent to the original email address for confirmation.</p>";
							}
						}
						
						// Close the connection to mysql database
						mysqli_close($conn);
						  
					} else {
							// Display a message if any of the fields are left out.
							echo "<p> All fields are required. Press the return button and complete the form. </p>";
					}
				?>
			</div>
		</div>
	</body>
</html>
