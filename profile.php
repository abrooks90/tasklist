<?php
	@session_start(); //start session
	//see http://phpsec.org/projects/guide/4.html

	//prevent unauthorized access using back button
	if (!isset($_SESSION['authenticated'])) {
		session_regenerate_id();
		$_SESSION['authenticated'] = 0;
	}
?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>KSU Student Services | Profile</title>
		<link rel="stylesheet" type="text/css" href="registration_styles.css"/>
		<script src="http://code.jquery.com/jquery.min.js"></script>
		<script type="text/javascript">
			function formValidation(){
				//Check the number of checkboxes that are checked and assign them to variable n.
				var n = $( ".service:checkbox:checked" ).length;
				// If n is equal to 0, alert the user and don't allow form submission.
				if(n==0){
					alert("Please select at least one available service!")
					return false;
				}

				// If n is equal to 0, alert the user and don't allow form submission.
				n = $( ".availability:checkbox:checked" ).length;
				if(n==0){
					alert("Please select your availability!")
					return false;
				}

				n = $( ".days:checkbox:checked" ).length;
				if(n==0){
					alert("Please select your availability!")
					return false;
				}
			}

			//let user update profile information
			function makeEditable(){
				$(".editable").prop("readonly", false);
				$(".editable").prop("placeholder","");
				$(".editable").prop("hidden",false);
				$(".viewable").prop("hidden",true);
				}
		</script>
	</head>
	<body>
		<header>
			<h1>KSU Student Services</h1>
		</header>
		<div id="wrapper">
			<?php
				//include side navigation menu
				include "side_nav.php";
			?>
			<h3>User Profile</h3>

			<form class="profile" action="update_profile.php" id="profile" method="post" onsubmit="return formValidation()">
				<?php
					//include menu
					include "menu.php";
					// Check to see if there's a valid session. If not, display link to login page.
					if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
						echo "ERROR: To use this web site, you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
					}
					else {
						//create connection to database
						$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
						if (mysqli_connect_errno()){
							die('Cannot connect to database: ' . mysqli_connect_error($conn));
						}
						else{
							//gets profileID to use for profile
							$query = mysqli_prepare($conn,"SELECT profileID FROM profile where netID = ?")
								or die("Error: " . mysqli_error($conn));
							//binds session user to query
							mysqli_stmt_bind_param($query,"s",$_SESSION['user']);
							mysqli_stmt_execute($query)
								or die("Error: " . mysqli_error($conn));
							mysqli_stmt_bind_result($query,$profID);
							$result = mysqli_stmt_get_result($query);
							while($row=mysqli_fetch_array($result)){
								$profileID = $row['profileID'];
							}
							//close first query
							mysqli_stmt_close($query);

							//gets profile information from profile based on profileID
							$query = mysqli_prepare($conn,"SELECT netID, fname, lname, email, avail_days, avail_hours FROM profile WHERE profileID = ?")
								or die("Error: " . mysqli_error($conn));
							mysqli_stmt_bind_param($query,"i",$profileID);
							mysqli_stmt_execute($query)
								or die("Error: ". mysqli_error($conn));
							$result = mysqli_stmt_get_result($query);
							//closes second query
							mysqli_stmt_close($query);

							//gets svcID from services_offered based on profileID
							$query = mysqli_prepare($conn,"SELECT service_description from services INNER JOIN services_offered on services.svcID = services_offered.svcID WHERE profileID = ?")
								or die("Error: ". mysqli_error($conn));
							mysqli_stmt_bind_param($query,"i",$profileID);
							mysqli_stmt_execute($query)
								or die("Error: " . mysqli_error($conn));
							$result2 = mysqli_stmt_get_result($query);
							//closes third query
							mysqli_stmt_close($query);

							//gets service names from services based on svcIDs
							$query = mysqli_prepare($conn,"SELECT svcID, service_description from services")
								or die("Error: " . mysqli_error($conn));
							mysqli_stmt_execute($query)
								or die("Error: " . mysqli_error($conn));
							$result3 = mysqli_stmt_get_result($query);
							//closes fourth query
							mysqli_stmt_close($query);
							//closes connection
							mysqli_close($conn);

							//makes sure all queries were successful
							if(!$result && !$result2 && !$result3){
								echo "Error occurred in retrieving information.";
							}
							else{
								//use query information to display profile
								while($row = mysqli_fetch_array($result)){
									//displays netID, name, and email in a fieldset
									echo "<fieldset id='contact'>";
									//use the "pattern" and "title" fields to notify the end user of invalid input
									//NetID consists of alphanumerical input. Uppercase and lowercase and 0-9. Input should at least be 3 characters.
									echo "<label>KSU NetID:<input type='text' pattern='[A-Za-z0-9]{3,}' title='Please enter your alphanumerical NetID.' name='netId' required readonly placeholder='{$row['netID']}' value='{$row['netID']}'/></label><br>";
									//HTML5 has a built in pattern match for email
									$oldEmail = $row['email'];
									echo "<label>Email:<input type='email' name='email' class='editable' required readonly placeholder='{$row['email']}' value='{$row['email']}'/></label><br>";
									//First and last name are uppercase/lowercase with at least 3 letters.
									echo "<label>First Name:<input type='text' pattern='[A-Za-z]{3,}' title='Please enter a name with at least three letters.' name='firstName' class='editable' required readonly placeholder='{$row['fname']}' value='{$row['fname']}'/></label><br>";
									echo "<label>Last Name:<input type='text' pattern='[A-Za-z]{3,}' title='Please enter a name with at least three letters.' name='lastName' class='editable' required readonly placeholder='{$row['lname']}' value = '{$row['lname']}'/></label><br>";
									echo "</fieldset>";

									//displays string of services in a fieldset
									echo "<fieldset id='show_services' class='viewable'>";
									echo "<legend>Services:</legend>";
									$offered = array();
									while($row2 = mysqli_fetch_array($result2)){
										array_push($offered,$row2['service_description']);
									}
									echo implode(",",$offered);
									echo "</fieldset>";

									//when edit button is pressed, displays services as checkboxes similar to registration page
									echo "<fieldset id='service' class='editable' hidden>";
									echo "<legend>Select Available Days:</legend>";
									while($row3 = mysqli_fetch_array($result3)){
										echo "<label><input type='checkbox' class='service' value='{$row3['svcID']}' name='services[]'";
										//checks if service is offered and checks box if it is
										if(in_array($row3['service_description'],$offered)){
											echo "checked";
										}
										echo ">{$row3['service_description']}</label>";
									}
									echo "</fieldset>";

									//displays string of days marked available
									echo "<fieldset id='show_days' class='viewable'>";
									echo "<legend>Available Days:</legend>";
									echo $row['avail_days'];
									echo "</fieldset>";

									//when edit button is presssed, displays days as checkboxes similar to registration
									$days = explode(",",$row['avail_days']);
									echo '<fieldset id="days" hidden class="editable"><legend>Select Available Days:</legend>';
									echo '<label><input type="checkbox" class="days" value="Sun" ';
										//checks if day is offered and checks box if it is
										if(in_array("Sun",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Sun</label>';
									echo '<label><input type="checkbox" class="days" value="Mon" ';
										if(in_array("Mon",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Mon</label>';
									echo '<label><input type="checkbox" class="days" value="Tue" ';
										if(in_array("Tue",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Tue</label>';
									echo '<label><input type="checkbox" class="days" value="Wed" ';
										if(in_array("Wed",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Wed</label>';
									echo '<label><input type="checkbox" class="days" value="Thu" ';
										if(in_array("Thu",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Thu</label>';
									echo '<label><input type="checkbox" class="days" value="Fri" ';
										if(in_array("Fri",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Fri</label>';
									echo '<label><input type="checkbox" class="days" value="Sat" ';
										if(in_array("Sat",$days)){
											echo "checked";
										}
									echo ' name="days[]" >Sat</label>';
									echo "</fieldset>";

									//displays string of times marked available
									echo "<fieldset id='show_times' class='viewable'>";
									echo "<legend>Available Times:</legend>";
									echo $row['avail_hours'];
									echo "</fieldset>";

									//when edit button is pressed, displays times as checkboxes similar to registration
									$times = explode(",",$row['avail_hours']);
									echo '<fieldset id="availability" class="editable" hidden>';
									echo '<legend>Select Available Times:</legend>';
									echo '<label><input type="checkbox" class="availability" value="8AM-9AM" name="time[]" ';
										//checks if time is offered and checks box if it is
										if(in_array("8AM-9AM",$times)){
											echo "checked";
										}
									echo ">8AM-9AM</label>";
									echo '<label><input type="checkbox" class="availability" value="9AM-10AM" name="time[]" ';
										if(in_array("9AM-10AM",$times)){
											echo "checked";
										}
									echo ">9AM-10AM</label>";
									echo '<label><input type="checkbox" class="availability" value="11AM-12PM" name="time[]" ';
										if(in_array("11AM-12PM",$times)){
											echo "checked";
										}
									echo ">11AM-12PM</label>";
									echo '<label><input type="checkbox" class="availability" value="12PM-1PM" name="time[]" ';
										if(in_array("12PM-1PM",$times)){
											echo "checked";
										}
									echo ">12PM-1PM</label>";
									echo "<br>";
									echo '<label><input type="checkbox" class="availability" value="1PM-2PM" name="time[]" ';
										if(in_array("1PM-2PM",$times)){
											echo "checked";
										}
									echo ">1PM-2PM</label>";
									echo '<label><input type="checkbox" class="availability" value="2PM-3PM" name="time[]" ';
										if(in_array("2PM-3PM",$times)){
											echo "checked";
										}
									echo ">2PM-3PM</label>";
									echo '<label><input type="checkbox" class="availability" value="3PM-4PM" name="time[]" ';
										if(in_array("3PM-4PM",$times)){
											echo "checked";
										}
									echo ">3PM-4PM</label>";
									echo '<label><input type="checkbox" class="availability" value="4PM-5PM" name="time[]" ';
										if(in_array("4PM-5PM",$times)){
											echo "checked";
										}
									echo ">4PM-5PM</label>";
									echo "</fieldset>";

									echo '<fieldset id="svcSuggest" class="editable" hidden>';
									echo "<p><label for='svcSuggestion'>Suggest a service:</label><input type='text' pattern='{3,}' title='Enter a service suggestion greater than 3 characters.' name='svcSuggestion' id='svcSuggestion'></p>";
									echo "</fieldset>";
								}
								//adds profile and email for use in submission processing
								echo "<input type='hidden' name='profileID' value={$profileID} />";
								echo "<input type='hidden' name='oldEmail' value={$oldEmail} />";
							}
						}
				?>
				<!--make editable button-->
				<input type="button" onclick="makeEditable()" class="viewable" value="Edit Profile"/>
				<!--submit button, made viewable when make editable button is pressed-->
				<input type="submit" class="editable" value="Submit changes" hidden />
			</form>
		</div>
	<!-- Closing tag for our previous statement that checks for a valid session -->
	<?php } ?>
	</body>
</html>
