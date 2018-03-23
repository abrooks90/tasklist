<?php session_start();?>
<html>
	<head>
		<title>Task List</title>
  </head>
  <body>
	<?php
		$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
		if (mysqli_connect_errno()){
          die('Cannot connect to database: ' . mysqli_connect_error($conn));
		}
		else{
			if(!empty($_POST['taskID'])){
				$serviceNo = $_POST['taskID'];
				$_SESSION['taskID'] = $_POST['taskID'];
				if($query = mysqli_prepare($conn,"SELECT service_description, task_description, task_due_date FROM services INNER JOIN tasks ON services.svcID = tasks.svcID WHERE taskID = ?")){
					mysqli_stmt_bind_param($query,"i",$serviceNo);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query,$servicedesc,$taskdesc,$duedate);
					while (mysqli_stmt_fetch($query)){
						$service = $servicedesc;
						$_SESSION['taskdesc'] = $taskdesc;
						$_SESSION['duedate'] = $duedate;
					}
					mysqli_stmt_close($query);
				}
				if ($query = mysqli_prepare($conn,"SELECT fname, lname, email, netID FROM profile
													INNER JOIN services_offered on profile.profileID = services_offered.profileID
													INNER JOIN services on services_offered.svcID = services.svcID
													WHERE (service_description like ? and netID NOT LIKE ?)")) {
					mysqli_stmt_bind_param ($query, "ss", $service,$_SESSION['user']);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query, $FirstName, $LastName, $email,$netID);
					echo "<table id='transferRecipients'>";
					echo "<tr><th></th><th>Name</th><th>Email</th><th>NetID</th></tr>";
					while (mysqli_stmt_fetch($query)) {
						echo "<tr><td><input type='radio' name='recipient' value='{$email}'/></td>
								<td>{$FirstName} {$LastName}</td>
								<td>{$email}</input></td>
								<td>{$netID}</td></tr>";
					}
					echo "</table><br/>";
					echo "<input type='submit' value='Transfer'/>";
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
