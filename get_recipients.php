<?php 
	session_start();//start session
	
	//prevent unauthorized access using back button
	if (!isset($_SESSION['authenticated'])) {
		session_regenerate_id();
		$_SESSION['authenticated'] = 0;
	}
	
	// Check to see if there's a valid session. If not, display link to login page.
	if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
	    echo "ERROR: To use this web site, you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
	} else {
		//establish connection to database
		$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
			
		if (mysqli_connect_errno()){
			  die('Cannot connect to database: ' . mysqli_connect_error($conn));
		} else{
			//check that a task was selected
			if(!empty($_POST['taskID'])){
				//set taskID
				$serviceNo = $_POST['taskID'];
				//set session task for use in transfer processing
				$_SESSION['taskID'] = $_POST['taskID'];
				
				//get task information associated with taskID
				if($query = mysqli_prepare($conn,"SELECT service_description, task_description, task_due_date, requester_email FROM services INNER JOIN tasks ON services.svcID = tasks.svcID WHERE taskID = ?")){
					mysqli_stmt_bind_param($query,"i",$serviceNo);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query,$servicedesc,$taskdesc,$duedate,$remail);
					//set variables for processing
					while (mysqli_stmt_fetch($query)){
						$service = $servicedesc;
						$_SESSION['taskdesc'] = $taskdesc;
						$_SESSION['duedate'] = $duedate;
						$requester_email = $remail;
					}
					//close first query
					mysqli_stmt_close($query);
				}
				
				//select other users who offer service
				if ($query = mysqli_prepare($conn,"SELECT fname, lname, email, netID FROM profile
													INNER JOIN services_offered on profile.profileID = services_offered.profileID
													INNER JOIN services on services_offered.svcID = services.svcID
													WHERE (service_description like ? and netID NOT LIKE ? and email not like ?)")) {
					mysqli_stmt_bind_param ($query, "sss", $service,$_SESSION['user'],$requester_email);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query, $FirstName, $LastName, $email,$netID);
					mysqli_stmt_store_result($query);
					//make sure valid recipients exist
					if(mysqli_stmt_num_rows($query)==0){
						echo "<h2>No valid recipients found</h2>";
					}else{
						//display valid recipient information in pseudo-tabular form
						echo "<h2>Please choose a recipient</h2>";
						echo "<div class='trow'><div class='tcell'>Select</div><div class='tcell'>Name</div><div class='tcell'>Email</div><div class='tcell'>NetID</div></div>";
						while (mysqli_stmt_fetch($query)) {
							echo "<div class='trow'><div class='tcell'><input type='radio' name='recipient' value='{$email}'/></div><div class='tcell'>{$FirstName} {$LastName}</div><div class='tcell'>{$email}</input></div><div class='tcell'>{$netID}</div></div>";
						}
						echo "<input type='submit' value='Transfer'/>";
					}
					//close second query
					mysqli_stmt_close ($query);
				} else{
					die("Error: " . mysqli_error($conn));
				}
			} else {
				echo "No task specified";
			}
			//close connection
			mysqli_close($conn);
		}
	}
?>