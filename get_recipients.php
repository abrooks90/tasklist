<?php session_start();?>
<html>
	<head>
		<title>Task List</title>
  </head>
  <body>
	<?php
		$conn = new mysqli("localhost", $_SESSION['user'],$_SESSION['password'], "abrooks");
		if (mysqli_connect_errno()){
          die('Cannot connect to database: ' . mysqli_connect_error($conn));
		}
		else{
			if(!empty($_POST['taskID'])){
				$taskNo = $_POST['taskID'];
				$_SESSION['taskID'] = $_POST['taskID'];
				if($query = mysqli_prepare($conn,"SELECT svcID, task_description, task_due_date FROM tasks WHERE taskID = ?")){
					mysqli_stmt_bind_param($query,"i",$taskNo);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query,$serviceID,$taskdesc,$duedate);
					while (mysqli_stmt_fetch($query)){
						$service = $serviceID;
						$_SESSION['taskdesc'] = $taskdesc;
						$_SESSION['duedate'] = $duedate;
					}
					mysqli_stmt_close($query);
				}
				echo "<h3>Select a recipient:</h3><br/>";
				echo "<form class='tasks' action='tasktransfer.php' method='post' onsubmit='return checkrecipient()'><br/>";
				if ($query = mysqli_prepare($conn,"SELECT fname, lname, email, netID FROM profile
													INNER JOIN services_offered on profile.profileID = services_offered.profileID 
													WHERE (svcID like ? and netID NOT LIKE ?)")) {
					mysqli_stmt_bind_param ($query, "is", $service,$_SESSION['user']);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query, $FirstName, $LastName, $email,$netID);
					mysqli_stmt_store_result($query);
					if(mysqli_stmt_num_rows($query) == 0){
						echo "No valid recipients found<br/>";
						echo "</form><br/>";
					}else{
						echo "<table id='transferRecipients'>";
						echo "<tr><th></th><th>Name</th><th>Email</th><th>NetID</th></tr>";
						while (mysqli_stmt_fetch($query)) {
							echo "<tr><td><input type='radio' name='recipient' value='{$email}'/></td>
								<td>{$FirstName} {$LastName}</td>
								<td>{$email}</input></td>
								<td>{$netID}</td></tr>";
						}		
						echo "</table><br/>";
						echo "<p id='button'><input type='submit' value='Transfer'/></p>";
						echo "</form><br/>";
					}
					mysqli_stmt_close ($query);
				} else{
					echo "Error: " . mysqli_error($conn);
				}
			} else {
				echo "No task specified";
			}
			mysqli_close($conn);
		}
	?>
  </body>
</html>