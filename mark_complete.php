<?php
@session_start(); //start session

//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}
?>
<?xml version="1.0" encoding="ISO-8859-1"?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Task Complete</title>
		<link rel="stylesheet" type="text/css" href="registration_styles.css"/>
	</head>
	<body>
		<header>
			<h1>KSU Student Services</h1>
		</header>
		<div id="wrapper">
			<?php include "side_nav.php"; ?>
			<h3>Task complete</h3>
			<div id="results" class="tasks">
				<?php include "menu.php";
				// Check to see if there's a valid session. If not, display link to login page.
				if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
				    echo "ERROR: To use this web site, you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
				}
				else {
					//create connection to database
					$conn = new mysqli("localhost","student_user","my*password", "abrooks");
					if (mysqli_connect_errno()){
						die('Cannot connect to database: ' . mysqli_connect_error($conn));
					}
					else{
						//verify that a task was selected
						if(!empty($_POST['markcomplete'])){
							//get taskID for updating tables
							$taskID = $_POST['markcomplete'];
							
							//mark task as complete in database, removes from assigned tasks view
							$query = mysqli_prepare($conn,"UPDATE tasks SET complete = 'Y' WHERE taskID = ?");
							//bind taskID to query
							mysqli_stmt_bind_param($query,"i",$taskID);
							mysqli_stmt_execute($query);
							mysqli_stmt_close($query);
							if(mysqli_errno($conn)){
								die ("Error: " . mysqli_error($conn));
							}else{
								//provide statement to user indicating completion on current date
								echo "<div>The task has been completed on " . date("m/d/Y") . ".</div>";
								
								//get email address for user requesting task
								if($query1 = mysqli_prepare($conn,"SELECT requester_email, task_description FROM tasks where taskID = ?")){
									mysqli_stmt_bind_param($query1,"i",$taskID);
									mysqli_stmt_execute($query1);
									mysqli_stmt_bind_result($query1,$email,$task_description);
									mysqli_stmt_store_result($query1);
									while(mysqli_stmt_fetch($query1)){
										$email = $email;
										$task = $task_description;
									}
									mysqli_stmt_close($query1);
									
									//send email indicating task completion on current date
									$to = $email;
									$subject = "Task complete";
									$message = "The task " . $task . " has been completed on " . date('m/d/Y') . ".";
									if(mail($to,$subject,$message)){
										echo "An email has been sent to " . $email . "<br/>";
									}else{
										//let user know email failed and to notify requester 
										echo "Email notification failed. Please notify requester.";
									}
								}else{
									die ("Error: " . mysqli_connect_errno($conn));
								}
							}
							//close connection
							mysqli_close($conn);
						}else{
							//message that no task was selected
							echo "<h2>Please select task</h2>";
						}
					}
				}
				?>
			</div>
		</div>
	</body>
</html>