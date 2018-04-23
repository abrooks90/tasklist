<?php
	@session_start(); //start session

	//see http://phpsec.org/projects/guide/4.html
	if (!isset($_SESSION['authenticated'])) {
		session_regenerate_id();
		$_SESSION['authenticated'] = 0;
	}
?>
<html>
	<head>
		<title>Transfer task</title>
		<link rel="stylesheet" type="text/css" href="registration_styles.css"/>
  </head>
  <body>
		<header>
			<h1>KSU Student Services</h1>
		</header>
		<div id="wrapper">
			<?php
				//include side navigation page
				include "side_nav.php";
			?>
			<h3>Transfer a Task</h3>
			<div id="results" class="tasks">
			<?php
				//include menu page
				include "menu.php";

				// Check to see if there's a valid session. If not, display link to login page.
				if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
				    echo "ERROR: To use this web site, you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
				}
				else {
					//checks that a recipient was selected
					if(!empty($_POST['recipient'])){
						//sets email for recipient to send notification
						$email = $_POST['recipient'];

						//establishes connection to database
						$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
						if (mysqli_connect_errno()){
							die('Cannot connect to database: ' . mysqli_connect_error($conn));
						} else{
							//gets profileID of recipient
							if($query = mysqli_prepare($conn,"SELECT profileID from profile where email = ?")){
								mysqli_stmt_bind_param($query,"s",$email);
								mysqli_stmt_execute($query)
									or die("Error: " . mysqli_error($conn));
								mysqli_stmt_bind_result($query,$profileID);
								while(mysqli_stmt_fetch($query)){
									$profileID = $profileID;
								}
								//closes first query
								mysqli_stmt_close($query);
							}else{
								die("Error: " . mysqli_error($conn));
							}

							//update tasks database based on new assigned_user
							if($query = mysqli_prepare($conn,"UPDATE tasks SET profileID = ? WHERE taskID = ?")){
								mysqli_stmt_bind_param($query,"ii",$profileID,$_SESSION['taskID']);
								mysqli_stmt_execute($query)
									or die("Error: " . mysqli_error($conn));
								//closes second query
								mysqli_stmt_close($query);

								echo "<div>The task was successfully transferred.</div>";

								//emails new assigned_user of task transfer
								$to = $email;
								$subject = "Task transferred";
								$msg = "The following task has been transferred to you: ";
								$msg.= $_SESSION['taskdesc'] . " Due: " . $_SESSION['duedate'];
								$body = $msg;
								if(mail($to,$subject,$body)){
									echo "<div>An email has been sent to " . $email . "</div>";
								}else{
									echo "Email delivery failed. Please notify recipient.";
								}
							}else{
								die("Error: " . mysqli_error($conn));
							}
						}
						//closes connection
						mysqli_close($conn);
					} else{
						echo "<h2>Please choose a recipient</h2>";
					}
				}
			?>
			</div>
		</div>
	</body>
</html>
